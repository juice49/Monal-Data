<?php
namespace Fruitful\Data\Repositories;
/**
 * Fruitful Data Sets Repository.
 *
 * The Fruitful System's implementation of the DataSetsRepository.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Repositories\DataSetsRepository;
use Fruitful\Data\Models\DataSet;

class FruitfulDataSetsRepository implements DataSetsRepository
{
	/**
	 * The repository's messages.
	 *
	 * @var		 Fruitful\Core\Contracts\MessagesInterface
	 */
	protected $messages;

	/**
	 * An instance of the Components library. 
	 *
	 * @var		 Fruitful\Data\Libraries\ComponentsInterface
	 */
	protected $components;

	/**
	 * An instance the of the Data Set Templates Repository.
	 *
	 * @var		 Fruitful\Data\Repositories\DataSetTemplatesRepository
	 */
	protected $data_set_templates_repo;

	/**
	 * The database table the repository uses.
	 *
	 * @var		String
	 */
	protected $table = 'data_sets';

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Fruitful\Core\Contracts\MessagesInterface');
		$this->components = \App::make('Fruitful\Data\Libraries\ComponentsInterface');
		$this->data_set_templates_repo = \App::make('Fruitful\Data\Repositories\DataSetTemplatesRepository');
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
	 * Return a new Data Set model.
	 *
	 * @return	Fruitful\Data\Models\DataSet
	 */
	public function newModel()
	{
		return \App::make('Fruitful\Data\Models\DataSet');
	}

	/**
	 * Check a Data Set model validates for storage.
	 *
	 * @param	Fruitful\Data\Models\DataSet
	 * @return	Boolean
	 */
	public function validatesForStorage(DataSet $data_set)
	{
		// Allow alpha, numeric, hypens, underscores and space characters.
		\Validator::extend('data_set_name', function($attribute, $value, $parameters)
		{
			return preg_match('/^[a-z0-9 \-_]+$/i', $value) ? true : false;
		});
		$unique_exception = ($data_set->ID()) ? ',' . $data_set->ID() : null;
		$validation_rules = array(
			'name' => 'required|max:100|data_set_name|unique:data_sets,name' . $unique_exception,
			'template_id' => 'required|numeric',
			'component' => 'required|not_in:0',
		);
		$validation_messages = array(
			'name.required' => 'You need to give this Data Set a Name.',
			'name.max' => 'The Name of this Data Set must be no more than 100 characters long.',
			'name.data_set_name' => 'The Name of this Data Set can only contain letters, numbers, spaces, underscores and hyphens.',
			'name.unique' => 'Aw shucks! This Name has already been used.',
			'template_id.required' => 'This Data Set needs to implement a Data Set Template.',
			'template_id.numeric' => 'The ID of the Data Set Template that this Data Set implements must be a numeric value.',
			'component.required' => 'You need to set a Component type for this Data Set.',
			'component.not_in' => 'You need to set a Component type for this Data Set.',
		);
		if ($data_set->validates($validation_rules, $validation_messages)) {
			return true;
		} else {
			$this->messages->add($data_set->messages()->toArray());
			return false;
		}
	}

	/**
	 * Encode a Data Set model so it is ready to be stored in the
	 * repository.
	 *
	 * @param	Fruitful\Data\Models\DataSet
	 * @return	Array
	 */
	protected function encodeForStorage(DataSet $data_set)
	{
		$data_set_template = $this->data_set_templates_repo->retrieve($data_set->templateID());
		$stripped_values = $this->components->make($data_set_template->componentURI())
								->stripImplementationValues($data_set->componentValues());
		return array(
			'name' => $data_set->name(),
			'template' => $data_set->templateID(),
			'content' => $stripped_values,
		);
	}

	/**
	 * Decode a Data Set repository entry into its model class.
	 *
	 * @param	stdClass
	 * @return	Fruitful\Data\Models\DataSet
	 */
	protected function decodeFromStorage($result)
	{
		$data_set = $this->newModel();
		$data_set->setID($result->id);
		$data_set->setName($result->name);
		$data_set->setTemplateID($result->template);
		if ($data_set_template = $this->data_set_templates_repo->retrieve($result->template)) {
			$dressed_values = $this->components->make($data_set_template->componentURI())
									->dressImplementationValues($result->content);
			$data_set->setComponent($data_set_template->componentURI());
			$data_set->setComponentSettings($data_set_template->componentSettings());
			$data_set->setComponentValues($dressed_values);
		}
		return $data_set;
	}

	/**
	 * Retrieve an instance/s from the repository.
	 *
	 * @param	Integer
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Models\DataSet
	 */
	public function retrieve($key = null)
	{
		$query = \DB::table($this->table);
		if (!$key) {
			$results = $query->select('*')->get();
			$data_sets = \App::make('Illuminate\Database\Eloquent\Collection');
			foreach ($results as $result) {
				$data_sets->add($this->decodeFromStorage($result));
			}
			return $data_sets;
		} else {
			if ($result = $query->where('id', '=', $key)->first()) {
				return $this->decodeFromStorage($result);
			}
		}
		return false;
	}

	/**
	 * Write a Data Set model to the repository.
	 *
	 * @param	Fruitful\Data\Models\DataSet
	 * @return	Boolean
	 */
	public function write(DataSet $data_set)
	{
		if ($this->validatesForStorage($data_set)) {
			$encoded = $this->encodeForStorage($data_set);
			if ($data_set->ID()) {
				$encoded['updated_at'] = date('Y-m-d H:i:s');
				\DB::table($this->table)->where('id', '=', $data_set->ID())->update($encoded);
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