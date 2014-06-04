<?php
namespace Monal\Data\Models;
/**
 * Monal Data Stream Entry.
 *
 * The Monal System's implementation of the DataStreamEntry model.
 *
 * @author	Arran Jacques
 */

use Monal\Data\Models\DataStreamEntry;
use Monal\Data\Models\DataSet;

class MonalDataStreamEntry implements DataStreamEntry, \IteratorAggregate, \ArrayAccess
{
	/**
	 * An array of the model's Data Sets.
	 *
	 * @var		Array
	 */
	protected $data_sets = array();

	/**
	 * An array that is used when the Model is used as an array. The
	 * array is a simplified version of the Data Sets that make up the
	 * Entry.
	 *
	 * @var		Array
	 */
	protected $items = null;

	/**
	 * An array that summarises each Data Set that makes up the entry.
	 *
	 * @var		Array
	 */
	protected $summarised_data_sets = null;

	/**
	 * Return an array that summarises each Data Set that makes up the
	 * entry.
	 *
	 * @return	Array
	 */
	public function summariseDataSets()
	{
		if ($this->summarised_data_sets === null) {
			$components = \App::make('Monal\Data\Libraries\ComponentsInterface');
			$this->summarised_data_sets = array();
			foreach ($this->data_sets as $key => $data_set) {
				$value = $components->make($data_set->componentURI())->summariseValues($data_set->componentValues());
				$this->summarised_data_sets[$data_set->name()] = $value;
			}
		}
		return $this->summarised_data_sets;
	}

	/**
	 * Create an array that is used when the Model is used as an array.
	 * The array is a simplified version of the Data Sets that make up
	 * the Entry.
	 *
	 * @return	Void
	 */
	public function buildIteratorArray()
	{
		$components = \App::make('Monal\Data\Libraries\ComponentsInterface');
		$this->items = array();
		foreach ($this->data_sets as $key => $data_set) {
			$value = $components->make($data_set->componentURI())->stripImplementationValues($data_set->componentValues());
			$this->items[$data_set->name()] = $value;
		}
	}

	/**
	 * Return an array of items to iterate over if the Model is being
	 * treated as an array.
	 *
	 * @return	Array
	 */
	public function getIterator()
	{
		if ($this->items === null) {
			$this->buildIteratorArray();
		}
		return new \ArrayIterator($this->items);
	}

	/**
	 * Determine if an item exists at an offset if the Model is being
	 * treated as an array.
	 *
	 * @param	Mixed
	 * @return	Boolead
	 */
	public function offsetExists($key)
	{
		if ($this->items === null) {
			$this->buildIteratorArray();
		}
		return array_key_exists($key, $this->items);
	}

	/**
	 * Get an item at a given offset if the Model is being treated as an
	 * array.
	 *
	 * @param	Mixed
	 * @return	Mixed
	 */
	public function offsetGet($key)
	{
		if ($this->items === null) {
			$this->buildIteratorArray();
		}
		return $this->items[$key];
	}

	/**
	 * Set the item at a given offset if the Model is being treated as
	 * an array.
	 *
	 * @param	Mixed
	 * @param	Mixed
	 * @return	Void
	 */
	public function offsetSet($key, $value)
	{
		if ($this->items === null) {
			$this->buildIteratorArray();
		}
		if (is_null($key)) {
			$this->items[] = $value;
		}
		else {
			$this->items[$key] = $value;
		}
	}

	/**
	 * Unset the item at a given offset if the Model is being treated as
	 * an array.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function offsetUnset($key)
	{
		if ($this->items === null) {
			$this->buildIteratorArray();
		}
		unset($this->items[$key]);
	}

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
	 * @param	Monal\Data\Models\DataSet
	 * @return	Void
	 */
	public function addDataSet(DataSet $data_set)
	{
		array_push($this->data_sets, $data_set);
	}

	/**
	 * Return a GUI for the model.
	 *
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function view(array $settings = array())
	{
		$data_sets = $this->data_sets;
		$show_validation = isset($settings['show_validation']) ? $settings['show_validation'] : false;
		return \View::make(
			'data::models.data_stream_entry',
			compact(
				'data_sets',
				'show_validation'
			)
		);
	}
}