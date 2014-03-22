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
		// Allow alpha, numeric, hypens, underscores and space characters, and must contain at least 1 alpha character.
		\Validator::extend('data_stream_template_name', function($attribute, $value, $parameters)
		{
			return (preg_match('/^[a-z0-9 \-_]+$/i', $value) AND preg_match('/[a-zA-Z]/', $value)) ? true : false;
		});
		// Allow alpha, hypens and underscores, and must contain at least 1 alpha character.
		\Validator::extend('table_prefix', function($attribute, $value, $parameters)
		{
			return (preg_match('/^[a-z\-_]+$/i', $value) AND preg_match('/[a-zA-Z]/', $value)) ? true : false;
		});

		$unique_exception = ($data_stream_template->ID()) ? ',' . $data_stream_template->ID() : null;
		$validation_rules = array(
			'name' => 'required|max:100|data_stream_template_name|unique:data_stream_templates,name' . $unique_exception,
			'table_prefix' => 'table_prefix',
		);
		$validation_messages = array(
			'name.required' => 'You need to give this Data Stream Template a Name.',
			'name.max' => 'The Name of this Data Stream Template must be no more than 100 characters long.',
			'name.data_stream_template_name' => 'The Name of this Data Stream Template can only contain letters, numbers, spaces, underscores and hyphens, and must contain at least 1 letter.',
			'name.unique' => 'Aw shucks! This Name has already been used.',
			'table_prefix.table_prefix' => 'The Table Prefix for this Data Stream Template can only contain letters, hypens and underscores, and must contain at least one letter.',
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
			'table_prefix' => $data_stream_template->tablePrefix(),
			'data_set_templates' => array(),
		);
		foreach ($data_stream_template->dataSetTemplates() as $data_set_template) {
			array_push(
				$encoded['data_set_templates'],
				array(
					'uri' => $data_set_template->URI(),
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
	 * @param	stdClass
	 * @return	Fruitful\Data\Models\DataStreamTemplate
	 */
	public function decodeFromStorage($results)
	{
		$data_stream_template = $this->newModel();
		$data_stream_template->setID($results->id);
		$data_stream_template->setName($results->name);
		$data_stream_template->setTablePrefix($results->table_prefix);
		$data_set_templates = json_decode($results->data_set_templates, true);
		foreach ($data_set_templates as $encoded_data_set_template) {
			$data_set_template = \App::make('Fruitful\Data\Models\DataSetTemplate');
			$data_set_template->setURI($encoded_data_set_template['uri']);
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
		$query = \DB::table($this->table)->select('*');
		if (!$key) {
			$results = $query->get();
			$data_stream_templates = \App::make('Illuminate\Database\Eloquent\Collection');
			foreach ($results as &$result) {
				$data_stream_templates->add($this->decodeFromStorage($result));
			}
			return $data_stream_templates;
		} else {
			if ($result = $query->find($key)) {
				return $this->decodeFromStorage($result);
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
			$time = new \DateTime();
			$data = array(
				'name' => $encoded['name'],
				'table_prefix' => $encoded['table_prefix'],
				'data_set_templates' => $encoded['data_set_templates'],
				'updated_at' => $time->format('Y-m-d H:i:s'),
			);
			if ($data_stream_template->ID()) {
				\DB::table($this->table)->where('id', '=', $data_stream_template->ID())->update($data);
				return true;
			} else {
				$data['created_at'] = $time->format('Y-m-d H:i:s');
				\DB::table($this->table)->insert($data);
				return true;
			}
		}
		return false;
	}
}