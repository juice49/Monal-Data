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
	 * within a Data Stream Template.
	 *
	 * @param	Monal\Data\Models\DataStreamTemplate
	 * @return	Boolean / String
	 */
	public function update(DataStreamTemplate $from, DataStreamTemplate $to)
	{	
		if ($to->ID()) {
			$repository_name = \Text::snakeCaseString($from->tablePrefix());
			$repository_name .= \Text::snakeCaseString($from->name());

			// Create an array of Data Set Templates in the original Data Stream
			// Template.
			$original_repo_structure = array();
			foreach ($from->dataSetTemplates() as $original_data_set_template) {
				$original_repo_structure[$original_data_set_template->URI()] = $original_data_set_template;
			}

			// Create an array of Data Set Templates in the new Data Stream
			// Template.
			$updated_repo_structure = array();
			foreach ($to->dataSetTemplates() as $data_set_template) {
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

			// Loop through the Data Set Templates in the updated Data Stream
			// Template and identify where they have changed or new ones have been
			// added.
			$name_change_map = array();
			$cols_to_create = array();
			foreach ($to->dataSetTemplates() as $data_set_template) {

				// Check if the Data Set Template already has a corresponsing column
				// in the streams table.
				if (isset($original_repo_structure[$data_set_template->URI()])) {
					// It does have column.

					$original = $original_repo_structure[$data_set_template->URI()];

					// If the Data Set Template's name has been change then we need to
					// update the corresponding column's name, so add it to the list
					// column names to be updates.
					if ($original->name() !== $data_set_template->name()) {
						$name_change_map[\Text::snakeCaseString($original->name())] = \Text::snakeCaseString($data_set_template->name());
					}
				} else {
					// If the Data Set Template is new and doesn't have a corresponding
					// column then add it to the list columns to be created.
					$cols_to_create[$data_set_template->name()] = \Text::snakeCaseString($data_set_template->name());
				}
			}

			// Loop through the array of column names to be updated and update
			// each one accordingly.
			$temporary_col_names = array();
			$i = 0;
			foreach ($name_change_map as $old_col_name => $new_col_name) {

				// If there is already a column with the same name that this column is
				// to be given, but the existing column is also due to have its name
				// changed (meaning there won’t be a naming conflict after the process
				// is complete), then assign a temporary name to the current column.
				// After the existing column’s name has been updated we can then come
				// back to this one and update it.
				if (isset($name_change_map[$new_col_name])) {
					$temp_name = 'temp' . $i;
					$temporary_col_names[$temp_name] = $new_col_name;
					\Schema::table($repository_name, function($table) use($old_col_name, $temp_name)
					{
						$table->renameColumn($old_col_name, $temp_name);
					});
				// Else just rename the column.
				} else {
					\Schema::table($repository_name, function($table) use($old_col_name, $new_col_name)
					{
						$table->renameColumn($old_col_name, $new_col_name);
					});
				}
				$i++;
			}

			// Loop through any columns with temporary names and update their
			// names accordingly.
			foreach ($temporary_col_names as $temp_col_name => $new_col_name) {
				\Schema::table($repository_name, function($table) use($temp_col_name, $new_col_name)
				{
					$table->renameColumn($temp_col_name, $new_col_name);
				});
			}

			// Loop through the array of column to be created and create each one
			// in turn.
			foreach ($cols_to_create as $column_name) {
				\Schema::table($repository_name, function($table) use($column_name)
				{
					$table->longtext($column_name);
				});
			}

			// Check if the Data Stream Template's name has been changed. If so
			// update its corresponding table's name.
			$updated_repo_name =  \Text::snakeCaseString($to->tablePrefix());
			$updated_repo_name .= \Text::snakeCaseString($to->name());
			if ($repository_name != $updated_repo_name) {
				\Schema::rename($repository_name, $updated_repo_name);
			}
			return true;
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

	/**
	 * Add a new Entry to a Data Streams repository.
	 *
	 * @param	Monal\Data\Models\DataStreamTemplate
	 * @param	Monal\Data\Models\DataStreamEntry
	 * @param	Integer
	 */
	public function updateEntry(DataStreamTemplate $data_stream_template, DataStreamEntry $entry) {
		$repository_name = \Text::snakeCaseString($data_stream_template->tablePrefix());
		$repository_name .= \Text::snakeCaseString($data_stream_template->name());
		$encoded_entry = array(
			'updated_at' => date('Y-m-d H:i:s'),
		);
		$components = \App::make('Monal\Data\Libraries\ComponentsInterface');
		foreach ($data_stream_template->dataSetTemplates() as $key => $data_set_template) {
			$value = $components->make($data_set_template->componentURI())
				->stripImplementationValues($entry->dataSets()[$key]->componentValues());
			$encoded_entry[\Text::snakeCaseString($data_set_template->name())] = $value;
		}
		return \DB::table($repository_name)->where('rel', '=', $entry->ID())->update($encoded_entry);
	}
}