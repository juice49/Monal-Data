<?php
namespace Fruitful\Data\Repositories;
/**
 * Eloquent Data Stream Templates Repository.
 *
 * The Fruitful System's implementation of the
 * DataStreamTemplatesRepository using Laravel’s Eloquent ORM.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Repositories\DataStreamTemplatesRepository;
use Fruitful\Data\Models\DataStreamTemplate;

class EloquentDataStreamTemplatesRepository extends \Eloquent implements DataStreamTemplatesRepository
{
	/**
	 * The repository's messages.
	 *
	 * @var		 Fruitful\Core\Contracts\MessagesInterface
	 */
	protected $messages;

	/**
	 * The database table the repository uses.
	 *
	 * @var		String
	 */
	protected $table = 'data_stream_templates';

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
	 * Return the repository's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages()
	{
		return $this->messages->get();
	}

	/**
	 * Return a new Data Stream Template model.
	 *
	 * @return	Fruitful\Data\Models\DataStreamTemplate
	 */
	public function newModel()
	{
		return \App::make('Fruitful\Data\Models\DataStreamTemplate');
	}

	/**
	 * Check a Data Stream Template model validates for storage.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Boolean
	 */
	public function validatesForStorage(DataStreamTemplate $data_stream_template)
	{
		$unique_exception = ($data_stream_template->ID()) ? ',' . $data_stream_template->ID() : null;
		$validation_rules = array(
			'name' => 'required|username|unique:data_stream_templates,name' . $unique_exception,
		);
		$validation_messages = array(
			'name.required' => 'You need to give this Data Stream Template a Name.',
			'name.username' => 'The Name can only contain letters, numbers, spaces, underscores and hyphens.',
			'name.unique' => 'Sorry, it looks like someone beat you to the punch as this Name has already taken.',
		);
		if ($data_stream_template->validates($validation_rules, $validation_messages)) {
			return true;
		} else {
			$this->messages->add($data_stream_template->messages()->toArray());
			return false;
		}
	}

	/**
	 * Encode a Data Stream Template model so it is ready to be stored in
	 * the repository.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Array
	 */
	protected function encodeForStorage(DataStreamTemplate $data_stream_template)
	{
		$encoded = array(
			'name' => $data_stream_template->name(),
			'data_set_templates' => array(),
		);
		foreach ($data_stream_template->dataSetTemplates() as $data_set_template) {
			array_push(
				$encoded['data_set_templates'],
				array(
					'name' => $data_set_template->name(),
					'component' => $data_set_template->componentURI(),
					'component_settings' => $data_set_template->componentSettings(),
				)
			);
		}
		$encoded['data_set_templates'] = json_encode($encoded['data_set_templates'], JSON_FORCE_OBJECT);
		return $encoded;
	}

	/**
	 * Decode a Data Stream Template repository entry into its model
	 * class.
	 *
	 * @return	Fruitful\Data\Models\DataStreamTemplate
	 */
	protected function decodeFromStorage()
	{
		$data_stream_template = $this->newModel();
		$data_stream_template->setID($this->id);
		$data_stream_template->setName($this->name);
		$data_set_templates = json_decode($this->data_set_templates, true);
		foreach ($data_set_templates as $encoded_data_set_template) {
			$data_set_template = \App::make('Fruitful\Data\Models\DataSetTemplate');
			$data_set_template->setName($encoded_data_set_template['name']);
			$data_set_template->setComponent($encoded_data_set_template['component']);
			$data_set_template->setComponentSettings($encoded_data_set_template['component_settings']);
			$data_stream_template->addDataSetTemplate($data_set_template);
		}
		return $data_stream_template;
	}

	/**
	 * Retrieve an instance/s from the repository.
	 *
	 * @param	Integer
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Models\DataStreamTemplate
	 */
	public function retrieve($key = null)
	{
		if (!$key) {
			$results = self::all();
			$data_stream_templates = \App::make('Illuminate\Database\Eloquent\Collection');
			foreach ($results as &$result) {
				$data_stream_templates->add($result->decodeFromStorage());
			}
			return $data_stream_templates;
		} else {
			if ($result = self::find($key)) {
				return $result->decodeFromStorage();
			}
		}
		return false;
	}

	/**
	 * Write a Data Stream Template model to the repository.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Boolean
	 */
	public function write(DataStreamTemplate $data_stream_template)
	{
		if ($this->validatesForStorage($data_stream_template)) {
			$encoded = $this->encodeForStorage($data_stream_template);
			if ($data_stream_template->ID()) {
				if (
					$this->where('id', '=', $data_stream_template->ID())->update(
							array(
							'name' => $encoded['name'],
							'data_set_templates' => $encoded['data_set_templates'],
						)
					)
				) {
					return true;
				}
			} else {
				$entry = new self;
				$entry->name = $encoded['name'];
				$entry->data_set_templates = $encoded['data_set_templates'];
				return $entry->save() ? true : false;
			}
		}
		return false;
	}
}