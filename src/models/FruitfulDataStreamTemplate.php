<?php
namespace Fruitful\Data\Models;
/**
 * Fruitful Data Stream Template.
 *
 * The Fruitful System's implementation of the DataStreamTemplate
 * interface.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Models\DataStreamTemplate;
use Fruitful\Data\Libraries\DataSetTemplate;

class FruitfulDataStreamTemplate implements DataStreamTemplate
{
	/**
	 * The Data Stream Template's messages.
	 *
	 * @var		 Fruitful\Core\Contracts\MessagesInterface
	 */
	protected $messages;

	/**
	 * The Data Stream Template's ID.
	 *
	 * @var		Integer
	 */
	protected $id = null;

	/**
	 * The Data Stream Template's Name.
	 *
	 * @var		String
	 */
	protected $name = null;

	/**
	 * A collection of Data Set Templates that make up the Data Stream
	 * Template.
	 *
	 * @var		Illuminate\Database\Eloquent\Collection
	 */
	protected $data_set_templates;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Fruitful\Core\Contracts\MessagesInterface');
		$this->data_set_templates = \App::make('Illuminate\Database\Eloquent\Collection');
	}

	/**
	 * Return the Data Stream Template's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages()
	{
		return $this->messages->get();
	}

	/**
	 * Return the Data Stream Template's ID.
	 *
	 * @return	Integer
	 */
	public function ID()
	{
		return $this->id;
	}

	/**
	 * Return the Data Stream Template's Name.
	 *
	 * @return	String
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * Return the collection of Data Set Templates attached to the Data
	 * Stream Template. 
	 *
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function dataSetTemplates()
	{
		return $this->data_set_templates;
	}

	/**
	 * Set the ID for the Data Stream Template.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function setID($id)
	{
		$this->id = $id ? $id : null;
	}

	/**
	 * Set the Name for the Data Stream Template.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Add a Data Set Template to the Data Stream Template.
	 *
	 * @param	Fruitful\Data\Libraries\DataSetTemplate
	 * @return	Void
	 */
	public function addDataSetTemplate(DataSetTemplate $data_set_template)
	{
		$this->data_set_templates->add($data_set_template);
	}

	/**
	 * Discard all of the Data Set Templates attached to the Data Stream
	 * Template.
	 *
	 * @return	Void
	 */
	public function discardDataSetTemplates()
	{
		$this->data_set_templates = null;
		$this->data_set_templates = \App::make('Illuminate\Database\Eloquent\Collection');
	}

	/**
	 * Check the Data Stream Template validates against a set of given
	 * rules.
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
		$templates_validate = true;
		foreach ($this->data_set_templates as $data_set_template) {
			$validation_rules = array(
				'name' => 'required|username',
				'component' => 'required|not_in:0',
			);
			$validation_messages = array(
				'name.required' => 'You need to give this Data Set Template a Name.',
				'name.username' => 'The Name can only contain letters, numbers, spaces, underscores and hyphens.',
				'component.required' => 'You need to set a Component type for this Data Set Template.',
				'component.not_in' => 'You need to set a Component type for this Data Set Template.',
			);
			if (!$data_set_template->validates($validation_rules, $validation_messages)) {
				$this->messages->add(
					array(
						'error' => array(
							'There are some errors in the Data Sets you have used.',
						)
					)
				);
				$templates_validate = false;
			}
		}
		return ($stream_validates AND $templates_validate) ? true : false;
	}
}