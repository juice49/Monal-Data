<?php
namespace Fruitful\Data\Models;
/**
 * Fruitful Data Stream Entry.
 *
 * The Fruitful System's implementation of the DataStreamEntry model.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Models\DataStreamEntry;
use Fruitful\Data\Models\DataStreamTemplate;

class FruitfulDataStreamEntry implements DataStreamEntry
{
	/**
	 * An array of Data Sets that make up the entry model.
	 *
	 * @var		Array
	 */
	protected $data_sets = array();

	/**
	 * Use a Data Set Template to set what Data Sets the model has.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Void
	 */
	public function buildModelFromDataStreamTemplate(DataStreamTemplate $data_stream_template)
	{
		foreach ($data_stream_template->dataSetTemplates() as $data_set_template) {
			$data_set = \App::make('Fruitful\Data\Models\DataSet');
			$data_set->setName($data_set_template->name());
			$data_set->setComponent($data_set_template->componentURI());
			$data_set->setComponentSettings($data_set_template->componentSettings());
			array_push($this->data_sets, $data_set);
		}
	}

	/**
	 * Return the Data Sets the Entry has available.
	 *
	 * @return	Array
	 */
	public function dataSets()
	{
		return $this->data_sets;
	}

	/**
	 * Return the Entry's interface.
	 *
	 * @param	Boolean
	 * @return	Illuminate\View\View
	 */
	public function view($show_validation_messages = false)
	{
		$data_sets = $this->data_sets;
		return \View::make(
			'data::data_stream_entries.entry',
			compact(
				'show_validation_messages',
				'data_sets'
			)
		);
	}
}