<?php
namespace Fruitful\Data\Repositories;
/**
 * Eloquent Data Set Templates Repository.
 *
 * Implementation of the DataSetTemplatesRepository using Laravel’s
 * Eloquent ORM. Provides CURD functions for the repository.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Repositories\Contracts\DataSetTemplatesRepository;
use Fruitful\Data\Contracts\DataSetTemplateInterface;

class EloquentDataSetTemplatesRepository extends \Eloquent implements DataSetTemplatesRepository
{
	/**
	 * Instance of class implementing MessagesInterface.
	 *
	 * @var		 Fruitful\Core\Contracts\MessagesInterface
	 */
	protected $messages;

	/**
	 * Instance of class implementing DataSetTemplatesInterface.
	 *
	 * @var		 Fruitful\Data\Contracts\DataSetTemplatesInterface
	 */
	protected $data_set_templates;

	/**
	 * Database table the repository uses.
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
		$this->data_set_templates = \App::make('Fruitful\Data\Contracts\DataSetTemplatesInterface');
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
	 * Check the Data Set Template validates for storage.
	 *
	 * @param	Fruitful\Data\Contracts\DataSetTemplateInterface
	 * @return	Boolean
	 */
	public function validatesForStorage(DataSetTemplateInterface $data_set_template)
	{
		$unique_exception = ($data_set_template->id) ? ',' . $data_set_template->id : null;
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
	 * Encode a Data Set Template so it is ready to be stored in the repository.
	 *
	 * @param	Fruitful\Data\Contracts\DataSetTemplateInterface
	 * @return	Array
	 */
	protected function encodeForStorage(DataSetTemplateInterface $data_set_template)
	{
		return array(
			'name' => $data_set_template->name,
			'component' => $data_set_template->componentURI(),
			'component_settings' => json_encode($data_set_template->componentSettings(), JSON_FORCE_OBJECT),
		);
	}

	/**
	 * Decode a Data Set Template repository entry into its model class.
	 *
	 * @return	Fruitful\Data\Contracts\DataSetTemplateInterface
	 */
	protected function decodeFromStorage()
	{
		$data_set_template = $this->data_set_templates->make();
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
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Contracts\DataSetTemplateInterface
	 */
	public function retrieve($key = null)
	{
		if (!$key) {
			$results = self::all();
			$data_set_templates = new \Illuminate\Database\Eloquent\Collection;
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
	 * Write a Data Set Template to the repository.
	 *
	 * @param	Fruitful\Data\Contracts\DataSetTemplateInterface
	 * @return	Boolean
	 */
	public function write(DataSetTemplateInterface $data_set_template)
	{
		if ($this->validatesForStorage($data_set_template)) {
			$encoded = $this->encodeForStorage($data_set_template);
			if ($data_set_template->id) {
				if (
					$this->where('id', '=', $data_set_template->id)->update(
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