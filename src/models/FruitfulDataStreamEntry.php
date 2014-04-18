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
use Fruitful\Data\Models\DataSet;

class FruitfulDataStreamEntry implements DataStreamEntry
{
	/**
	 * An array of the model's Data Sets.
	 *
	 * @var		Array
	 */
	protected $data_sets = array();

	/**
	 * Return the model's Data Sets.
	 *
	 * @return	Array
	 */
	public function dataSets()
	{
		return $this->data_sets;
	}

	/**
	 * Add a new Data Set to the model.
	 *
	 * @param	Fruitful\Data\Models\DataSet
	 * @return	Void
	 */
	public function addDataSet(DataSet $data_set)
	{
		array_push($this->data_sets, $data_set);
	}

	/**
	 * Return a view of the model.
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