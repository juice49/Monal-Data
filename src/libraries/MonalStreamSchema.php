<?php
namespace Monal\Data\Libraries;
/**
 * Monal Stream Schema.
 *
 * Create and drop and update database tables that are used to store
 * entires in a Data Stream.
 *
 * @author	Arran Jacques
 */

use Monal\Data\Models\DataStreamTemplate;
use Monal\Data\Models\DataStreamEntry;

class MonalStreamSchema
{
	/**
	 * Create a new table from a Data Stream Template, which will then be
	 * used to store the entries of any Data Stream that implements that
	 * Data Stream Template.
	 *
	 * @param	Monal\Data\Models\DataStreamTemplate
	 * @return	Boolean / String
	 */
	public function build(DataStreamTemplate $data_stream_template)
	{
		$repository_name = \Text::snakeCaseString($data_stream_template->tablePrefix());
		$repository_name .= \Text::snakeCaseString($data_stream_template->name());
		if (!\Schema::hasTable($repository_name)) {
			\Schema::create($repository_name, function($table) use($data_stream_template) {
			    $table->increments('id');
			    $table->string('rel');
				foreach ($data_stream_template->dataSetTemplates() as $data_set_template) {
					$table->longtext(\Text::snakeCaseString($data_set_template->name()));
				}
				$table->timestamps();
			});
			return $repository_name;
		} else {
			return false;
		}
	}

	/**
	 * Update a Data Stream table by adding, removing or updating the
	 * table’s columns so that they correspond to the Data Set Templates
	 * within a Data Stream Template,
	 *
	 * @param	Monal\Data\Models\DataStreamTemplate
	 * @return	Boolean / String
	 */
	public function update(DataStreamTemplate $data_stream_template)
	{	
		$data_stream_templates_repo = \App::make('Monal\Data\Repositories\DataStreamTemplatesRepository');
		if ($data_stream_template->ID()) {
			if ($original_data_stream_template = $data_stream_templates_repo->retrieve($data_stream_template->ID())) {

				$repository_name = \Text::snakeCaseString($original_data_stream_template->tablePrefix());
				$repository_name .= \Text::snakeCaseString($original_data_stream_template->name());

				// Create an array of Data Set Templates in the original Data Stream
				// Template.
				$original_repo_structure = array();
				foreach ($original_data_stream_template->dataSetTemplates() as $original_data_set_template) {
					$original_repo_structure[$original_data_set_template->URI()] = $original_data_set_template;
				}

				// Loop through the Data Set Templates in the updated Data Stream
				// Template and identify where they have changed or new ones have been
				//added.
				$updated_repo_structure = array();
				foreach ($data_stream_template->dataSetTemplates() as $data_set_template) {

					// Check if the Data Set Template has a column in the existing table.
					if (isset($original_repo_structure[$data_set_template->URI()])) {
						$original = $original_repo_structure[$data_set_template->URI()];

						// If the Data Set Template's name has been changed, update the
						// corresponding column's name.
						if ($original->name() !== $data_set_template->name()) {
							\Schema::table($repository_name, function($table) use($original, $data_set_template)
							{
								$table->renameColumn(
							    	\Text::snakeCaseString($original->name()),
							    	\Text::snakeCaseString($data_set_template->name())
							    );
							});
						}
					} else {
						// Add a new column for new Data Set Templates.
						\Schema::table($repository_name, function($table) use($data_set_template)
						{
							$table->longtext(\Text::snakeCaseString($data_set_template->name()));
						});
					}
					$updated_repo_structure[$data_set_template->URI()] = $data_set_template->URI();
				}

				// Loop through the Data Set Templates in the original Data Stream
				// Template and see if any have been removed in the updated version.
				foreach ($original_repo_structure as $original_column_uri => $original_data_set_template) {
					// If a column has been removed then drop it from the repository.
					if (!isset($updated_repo_structure[$original_column_uri])) {
						\Schema::table($repository_name, function($table) use($original_data_set_template)
						{
							$table->dropColumn(\Text::snakeCaseString($original_data_set_template->name()));
						});
					}
				}

				// Check if the Data Stream Template's name has been changed. If so
				// update its corresponding table's name.
				$updated_repo_name =  \Text::snakeCaseString($data_stream_template->tablePrefix());
				$updated_repo_name .= \Text::snakeCaseString($data_stream_template->name());
				if ($repository_name != $updated_repo_name) {
					\Schema::rename($repository_name, $updated_repo_name);
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * Return all entires belonging to a given Data Stream.
	 *
	 * @param	Monal\Data\Models\DataStreamTemplate
	 * @param	Integer
	 * @return	Array
	 */
	public function getEntires(DataStreamTemplate $data_stream_template, $stream_id)
	{
		$repository_name = \Text::snakeCaseString($data_stream_template->tablePrefix());
		$repository_name .= \Text::snakeCaseString($data_stream_template->name());
		return \DB::table($repository_name)->select('*')->where('rel', '=', $stream_id)->get();
	}

	/**
	 * Add a new Entry to a Data Streams repository.
	 *
	 * @param	Monal\Data\Models\DataStreamTemplate
	 * @param	Monal\Data\Models\DataStreamEntry
	 * @param	Integer
	 * @return	Boolean
	 */
	public function addEntry(
		DataStreamTemplate $data_stream_template,
		DataStreamEntry $entry,
		$stream_id
	) {
		$repository_name = \Text::snakeCaseString($data_stream_template->tablePrefix());
		$repository_name .= \Text::snakeCaseString($data_stream_template->name());
		$encoded_entry = array(
			'rel' => (integer) $stream_id,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		);
		$components = \App::make('Monal\Data\Libraries\ComponentsInterface');
		foreach ($data_stream_template->dataSetTemplates() as $key => $data_set_template) {
			$value = $components->make($data_set_template->componentURI())
				->stripImplementationValues($entry->dataSets()[$key]->componentValues());
			$encoded_entry[\Text::snakeCaseString($data_set_template->name())] = $value;
		}
		return \DB::table($repository_name)->insert($encoded_entry);
	}
}