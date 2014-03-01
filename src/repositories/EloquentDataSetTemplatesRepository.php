<?php
namespace Fruitful\Data\Repositories;
/**
 * Eloquent Data Set Templates Repository.
 *
 * The Fruitful System's implementation of the
 * DataSetTemplatesRepository using Laravel’s Eloquent ORM.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Repositories\DataSetTemplatesRepository;
use Fruitful\Data\Models\DataSetTemplate;

class EloquentDataSetTemplatesRepository extends \Eloquent implements DataSetTemplatesRepository
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
		$unique_exception = ($data_set_template->ID()) ? ',' . $data_set_template->ID() : null;
		$validation_rules = array(
			'name' => 'required|username|unique:data_set_templates,name' . $unique_exception,
			'component' => 'required|not_in:0',
		);
		$validation_messages = array(
			'name.required' => 'You need to give this Data Set Template a Name.',
			'name.username' => 'The Name can only contain letters, numbers, spaces, underscores and hyphens.',
			'name.unique' => 'Sorry, it looks like someone beat you to the punch as this Name has already taken.',
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
	 * @return	Fruitful\Data\Models\DataSetTemplate
	 */
	protected function decodeFromStorage()
	{
		$data_set_template = $this->newModel();
		$data_set_template->setID($this->id);
		$data_set_template->setName($this->name);
		$data_set_template->setComponent($this->component);
		$data_set_template->setComponentSettings(json_decode($this->component_settings, true));
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
		if (!$key) {
			$results = self::all();
			$data_set_templates =  \App::make('Illuminate\Database\Eloquent\Collection');
			foreach ($results as &$result) {
				$data_set_templates->add($result->decodeFromStorage());
			}
			return $data_set_templates;
		} else {
			if ($result = self::find($key)) {
				return $result->decodeFromStorage();
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
				if (
					$this->where('id', '=', $data_set_template->ID())->update(
							array(
							'name' => $encoded['name'],
							'component' => $encoded['component'],
							'component_settings' => $encoded['component_settings'],
						)
					)
				) {
					return true;
				}
			} else {
				$this->name = $encoded['name'];
				$this->component = $encoded['component'];
				$this->component_settings = $encoded['component_settings'];
				return $this->save() ? true : false;
			}
		}
		return false;
	}
}