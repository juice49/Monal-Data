<?php
namespace Fruitful\Data\Libraries;
/**
 * Fruitful Stream Schema.
 *
 * Create and drop database tables for storing stream entries and
 * update table schema.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Models\DataStreamTemplate;

class FruitulStreamSchema
{
	/**
	 * Create a new table from a Data Stream Template, which will then be
	 * used to store the entries of any Data Stream that implements the
	 * Data Stream Template.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Boolean / String
	 */
	public function build(DataStreamTemplate $data_stream_template)
	{
		$repository_name = \Text::snakeCaseString($data_stream_template->tablePrefix());
		$repository_name .= \Text::snakeCaseString($data_stream_template->name());
		if (!\Schema::hasTable($repository_name)) {
			\Schema::create($repository_name, function($table) use($data_stream_template) {
			    $table->increments('id');
			    $table->string('stream');
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
	 * Update a Data Stream Template’s table’s schema by adding,
	 * removing, or updating table’s columns to match the Data Set
	 * Templates in the Data Stream Template.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Boolean / String
	 */
	public function update(DataStreamTemplate $data_stream_template)
	{	
		$data_stream_templates_repo = \App::make('Fruitful\Data\Repositories\DataStreamTemplatesRepository');
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
}