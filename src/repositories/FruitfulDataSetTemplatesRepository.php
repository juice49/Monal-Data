<?php
namespace Fruitful\Data\Repositories;
/**
 * Fruitful Data Set Templates Repository.
 *
 * The Fruitful System's implementation of the
 * DataSetTemplatesRepository.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Repositories\DataSetTemplatesRepository;
use Fruitful\Data\Models\DataSetTemplate;

class FruitfulDataSetTemplatesRepository implements DataSetTemplatesRepository
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
	protected $table = 'data_set_templates';

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
	 * Return a new Data Set Template model.
	 *
	 * @return	Fruitful\Data\Models\DataSetTemplate
	 */
	public function newModel()
	{
		return \App::make('Fruitful\Data\Models\DataSetTemplate');
	}

	/**
	 * Check the Data Set Template model validates for storage.
	 *
	 * @param	Fruitful\Data\Models\DataSetTemplate
	 * @return	Boolean
	 */
	public function validatesForStorage(DataSetTemplate $data_set_template)
	{
		// Allow alpha, numeric, hypens, underscores and space characters.
		\Validator::extend('data_set_template_name', function($attribute, $value, $parameters)
		{
			return preg_match('/^[a-z0-9 \-_]+$/i', $value) ? true : false;
		});
		$unique_exception = ($data_set_template->ID()) ? ',' . $data_set_template->ID() : null;
		$validation_rules = array(
			'name' => 'required|max:100|data_set_template_name|unique:data_set_templates,name' . $unique_exception,
			'component' => 'required|not_in:0',
		);
		$validation_messages = array(
			'name.required' => 'You need to give this Data Set Template a Name.',
			'name.max' => 'The Name of this Data Set Template must be no more than 100 characters long.',
			'name.data_set_template_name' => 'The Name of this Data Set Template can only contain letters, numbers, spaces, underscores and hyphens.',
			'name.unique' => 'Aw shucks! This Name has already been used.',
			'component.required' => 'You need to set a Component type for this Data Set Template.',
			'component.not_in' => 'You need to set a Component type for this Data Set Template.',
		);
		if ($data_set_template->validates($validation_rules, $validation_messages)) {
			return true;
		} else {
			$this->messages->add($data_set_template->messages()->toArray());
			return false;
		}
	}

	/**
	 * Encode a Data Set Template model so it is ready to be stored in
	 * the repository.
	 *
	 * @param	Fruitful\Data\Models\DataSetTemplate
	 * @return	Array
	 */
	protected function encodeForStorage(DataSetTemplate $data_set_template)
	{
		return array(
			'name' => $data_set_template->name(),
			'component' => $data_set_template->componentURI(),
			'component_settings' => json_encode($data_set_template->componentSettings(), JSON_FORCE_OBJECT),
		);
	}

	/**
	 * Decode a Data Set Template repository entry into its model class.
	 *
	 * @param	stdClass
	 * @return	Fruitful\Data\Models\DataSetTemplate
	 */
	protected function decodeFromStorage($result)
	{
		$data_set_template = $this->newModel();
		$data_set_template->setID($result->id);
		$data_set_template->setName($result->name);
		$data_set_template->setComponent($result->component);
		$data_set_template->setComponentSettings(json_decode($result->component_settings, true));
		return $data_set_template;
	}

	/**
	 * Retrieve an instance/s from the repository.
	 *
	 * @param	Integer
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Models\DataSetTemplate
	 */
	public function retrieve($key = null)
	{
		$query = \DB::table($this->table);
		if (!$key) {
			$results = $query->select('*')->get();
			$data_set_templates =  \App::make('Illuminate\Database\Eloquent\Collection');
			foreach ($results as $result) {
				$data_set_templates->add($this->decodeFromStorage($result));
			}
			return $data_set_templates;
		} else {
			if ($result = $query->where('id', '=', $key)->first()) {
				return $this->decodeFromStorage($result);
			}
		}
		return false;
	}

	/**
	 * Write a Data Set Template model to the repository.
	 *
	 * @param	Fruitful\Data\Models\DataSetTemplate
	 * @return	Boolean
	 */
	public function write(DataSetTemplate $data_set_template)
	{
		if ($this->validatesForStorage($data_set_template)) {
			$encoded = $this->encodeForStorage($data_set_template);
			if ($data_set_template->ID()) {
				$encoded['updated_at'] = date('Y-m-d H:i:s');
				\DB::table($this->table)->where('id', '=', $data_set_template->ID())->update($encoded);
				return true;
			} else {
				$encoded['created_at'] = date('Y-m-d H:i:s');
				$encoded['updated_at'] = date('Y-m-d H:i:s');
				\DB::table($this->table)->insert($encoded);
				return true;
			}
		}
		return false;
	}
}