<?php
namespace Monal\Data\Models;
/**
 * Monal Data Stream.
 *
 * The Monal System's implementation of the DataStream model.
 *
 * @author	Arran Jacques
 */

use Monal\Data\Models\DataStream;
use Monal\Data\Models\DataStreamTemplate;
use Monal\Data\Models\DataStreamEntry;

class MonalDataStream implements DataStream
{
	/**
	 * The Data Stream's messages.
	 *
	 * @var		 Monal\Core\Contracts\MessagesInterface
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
	 * @var		Monal\Data\Models\DataStreamTemplate
	 */
	public $template = null;

	/**
	 * An array of keys that correspond to Data Sets —- the values of
	 * which will be show when previewing entries for the Data Stream —-
	 * in the Data Stream Template that the Data Stream is implementing.
	 *
	 * @var		Array
	 */
	public $preview_column_ids = array();

	/**
	 * An array of the Data Stream's enteries.
	 *
	 * @var		Illuminate\Database\Eloquent\Collection
	 */
	protected $entries = null;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Monal\Core\Contracts\MessagesInterface');
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
	 * @return	Monal\Data\Models\DataStreamEntry
	 */
	public function newEntryModel()
	{
		$entry_model = \App::make('Monal\Data\Models\DataStreamEntry');
		foreach ($this->template->dataSetTemplates() as $data_set_template) {
			$data_set = \App::make('Monal\Data\Models\DataSet');
			$data_set->setName($data_set_template->name());
			$data_set->setComponent($data_set_template->componentURI());
			$data_set->setComponentSettings($data_set_template->componentSettings());
			$entry_model->addDataSet($data_set);
		}
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
	 * @return	Monal\Data\Models\DataStreamTemplate
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
	 * Return all of the Data Stream's enteries.
	 *
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function entries()
	{
		if (!$this->entries) {
			$entries = \App::make('Illuminate\Database\Eloquent\Collection');
			foreach (\StreamSchema::getEntires($this->template, $this->id) as $result) {
				unset($result->id);
				unset($result->rel);
				unset($result->created_at);
				unset($result->updated_at);
				$entry = $this->newEntryModel();
				$i = 0;
				foreach ($result as $key => $value) {
					$components = \App::make('Monal\Data\Libraries\ComponentsInterface');
					$dressed_values = $components->make($entry->dataSets()[$i]->componentURI())
										->dressImplementationValues($value);
					$entry->dataSets()[$i]->setComponentValues($dressed_values);
					$i++;
				}
				$entries->add($entry);
			}
			$this->entries = $entries;
		}
		return $this->entries;
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
	 * @param	Monal\Data\Models\DataStreamTemplate
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
	 * Determine if a Data Set has been defined as a preview column for
	 * the Data Stream.
	 *
	 * @param	Integer
	 * @return	Boolean
	 */
	public function hasPreviewColumn($id)
	{
		foreach ($this->preview_column_ids as $column_id) {
			if ($id === $column_id) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Add a new entry to the Data Stream.
	 *
	 * @param	Monal\Data\Models\DataStreamEntry
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