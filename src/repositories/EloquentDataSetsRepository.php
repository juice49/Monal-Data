<?php
namespace Fruitful\Data\Repositories;
/**
 * Eloquent Data Sets Repository.
 *
 * Implementation of the DataSetsRepository using Laravel’s
 * Eloquent ORM. Provides CURD functions for the repository.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Repositories\Contracts\DataSetsRepository;
use Fruitful\Data\Contracts\DataSetInterface;

class EloquentDataSetsRepository extends \Eloquent implements DataSetsRepository
{
	/**
	 * Instance of class implementing MessagesInterface.
	 *
	 * @var		 Fruitful\Core\Contracts\MessagesInterface
	 */
	protected $messages;

	/**
	 * Instance of class implementing ComponentsInterface.
	 *
	 * @var		 Fruitful\Data\Contracts\ComponentsInterface
	 */
	protected $components;

	/**
	 * Instance of class implementing DataSetsInterface.
	 *
	 * @var		 Fruitful\Data\Contracts\DataSetsInterface
	 */
	protected $data_sets;

	/**
	 * Instance of class implementing DataSetTemplatesRepository.
	 *
	 * @var		 Fruitful\Data\Repositories\Contracts\DataSetTemplatesRepository
	 */
	protected $data_set_templates_repo;

	/**
	 * Database table the repository uses.
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
		$this->components = \App::make('Fruitful\Data\Contracts\ComponentsInterface');
		$this->data_sets = \App::make('Fruitful\Data\Contracts\DataSetsInterface');
		$this->data_set_templates_repo = \App::make('Fruitful\Data\Repositories\Contracts\DataSetTemplatesRepository');
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
	 * Check a Data Set model validates for storage.
	 *
	 * @param	Fruitful\Data\Contracts\DataSetInterface
	 * @return	Boolean
	 */
	public function validatesForStorage(DataSetInterface $data_set)
	{
		$unique_exception = ($data_set->ID()) ? ',' . $data_set->ID() : null;
		$validation_rules = array(
			'name' => 'required|username|unique:data_sets,name' . $unique_exception,
			'template_id' => 'required|numeric',
			'component' => 'required|not_in:0',
		);
		$validation_messages = array(
			'name.required' => 'You need to give this Data Set a Name.',
			'name.username' => 'The Name can only contain letters, numbers, spaces, underscores and hyphens.',
			'name.unique' => 'Sorry, it looks like someone beat you to the punch as this Name has already taken.',
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
	 * @param	Fruitful\Data\Contracts\DataSetInterface
	 * @return	Array
	 */
	protected function encodeForStorage(DataSetInterface $data_set)
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
	 * @return	Fruitful\Data\Contracts\DataSetInterface
	 */
	protected function decodeFromStorage()
	{
		$data_set = $this->data_sets->make();
		$data_set->setID($this->id);
		$data_set->setName($this->name);
		$data_set->setTemplateID($this->template);
		if ($data_set_template = $this->data_set_templates_repo->retrieve($this->template)) {
			$dressed_values = $this->components->make($data_set_template->componentURI())
									->dressImplementationValues($this->content);
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
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Contracts\DataSetInterface
	 */
	public function retrieve($key = null)
	{
		if (!$key) {
			$entires = self::all();
			$data_sets = new \Illuminate\Database\Eloquent\Collection;
			foreach ($entires as $entry) {
				$data_sets->add($entry->decodeFromStorage());
			}
			return $data_sets;
		} else {
			if ($entry = self::find($key)) {
				return $entry->decodeFromStorage();
			}
		}
		return false;
	}

	/**
	 * Write a Data Set model to the repository.
	 *
	 * @param	Fruitful\Data\Contracts\DataSetInterface
	 * @return	Boolean
	 */
	public function write(DataSetInterface $data_set)
	{
		if ($this->validatesForStorage($data_set)) {
			$encoded = $this->encodeForStorage($data_set);
			if ($data_set->id) {
				if (
					$this->where('id', '=', $data_set->ID())->update(
						array(
							'name' => $encoded['name'],
							'content' => $encoded['content'],
						)
					)
				) {
					return true;
				}
			} else {
				$entry = new self;
				$entry->name = $encoded['name'];
				$entry->template = $encoded['template'];
				$entry->content = $encoded['content'];
				return $entry->save() ? true : false;
			}
		}
		return false;
	}
}