<?php
namespace Fruitful\Data\Models;
/**
 * Fruitful Data Stream.
 *
 * The Fruitful System's implementation of the DataStream model.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Models\DataStream;
use Fruitful\Data\Models\DataStreamTemplate;
use Fruitful\Data\Models\DataStreamEntry;

class FruitfulDataStream implements DataStream
{
	/**
	 * The Data Stream's messages.
	 *
	 * @var		 Fruitful\Core\Contracts\MessagesInterface
	 */
	protected $messages;

	/**
	 * The Data Stream's ID.
	 *
	 * @var		Integer
	 */
	public $id = null;

	/**
	 * The Data Stream's Name.
	 *
	 * @var		String
	 */
	public $name = null;

	/**
	 * A model of the Data Stream Template the Data Stream is
	 * implementing.
	 *
	 * @var		Fruitful\Data\Models\DataStreamTemplate
	 */
	public $template = null;

	/**
	 * An array of keys that correspond to Data Sets —- the values of
	 * which will be show when previewing entries for the Data Stream —-
	 * in the Data Set Template that the Data Stream is implementing.
	 *
	 * @var		Array
	 */
	public $preview_column_ids = array();

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Fruitful\Core\Contracts\MessagesInterface');
	}

	/**
	 * Return the Data Stream's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages()
	{
		return $this->messages->get();
	}

	/**
	 * Generate and return a new model of an entry for this stream.
	 *
	 * @return	Fruitful\Data\Models\DataStreamEntry
	 */
	public function newEntryModel()
	{
		$entry_model = \App::make('Fruitful\Data\Models\DataStreamEntry');
		$entry_model->buildModelFromDataStreamTemplate($this->template());
		return $entry_model;
	}

	/**
	 * Return the Data Stream's ID.
	 *
	 * @return	Integer
	 */
	public function ID()
	{
		return $this->id;
	}

	/**
	 * Return the Data Stream's Name.
	 *
	 * @return	String
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * Return the model of the Data Stream Template the Data Stream is
	 * implementing.
	 *
	 * @return	Fruitful\Data\Models\DataStreamTemplate
	 */
	public function template()
	{
		return $this->template;
	}

	/**
	 * Return the keys of the Data Sets in the Data Set Template that
	 * will be shown when previewing the entries of this Data Stream.
	 *
	 * @return	Array
	 */
	public function previewColumns()
	{
		return $this->preview_column_ids;
	}

	/**
	 * Set the ID for the Data Stream.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function setID($id)
	{
		$this->id = (integer) $id;
	}

	/**
	 * Set the Name for the Data Stream.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
	}

	/**
	 * Set the model of the Data Stream Template the Data Stream is
	 * implementing.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Void
	 */
	public function setTemplate(DataStreamTemplate $data_stream_template)
	{
		$this->template = $data_stream_template;
	}

	/**
	 * Define a Data Set to show when previewing entries for the Data
	 * Stream.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function addPreviewColumn($id)
	{
		$this->preview_column_ids[(integer) $id] = (integer) $id;
	}

	/**
	 * Discard all of the Preview Columns that have been set.
	 *
	 * @return	Void
	 */
	public function discardPreviewColumns()
	{
		$this->preview_column_ids = array();
	}

	/**
	 * Add a new entry to the Data Stream.
	 *
	 * @param	Fruitful\Data\Models\DataStreamEntry
	 * @return	Boolean
	 */
	public function addEntry(DataStreamEntry $entry)
	{
		$data_sets_validate = true;
		foreach ($entry->dataSets() as $data_set) {
			if (!$data_set->validates()) {
				$data_sets_validate = false;
				$this->messages->add(
					array(
						'error' => array(
							'The values you have entered below contain some errors. Please check them.',
						)
					)
				);
			}
		}
		if ($data_sets_validate) {
			if (\StreamSchema::addEntry($this->template, $entry, $this->id)) {
				return true;
			}
			$this->messages->add(
				array(
					'error' => array(
						'There was an error adding this entry to the Data Stream.',
					)
				)
			);
		}
		return false;
	}

	/**
	 * Check the Data Stream validates against a set of given rules.
	 *
	 * @param	Array
	 * @param	Array
	 * @return	Boolean
	 */
	public function validates(array $validation_rules = array(), array $validation_messages = array())
	{
		$data = array();
		$data['name'] = $this->name;
		$validation = \Validator::make($data, $validation_rules, $validation_messages);
		if ($validation->passes()) {
			$stream_validates = true;
		} else {
			$stream_validates = false;
			$this->messages->add($validation->messages()->toArray());
		}
		return ($stream_validates) ? true : false;
	}
}