<?php
namespace Monal\Data\Models;
/**
 * Monal Data Stream Template.
 *
 * The Monal System's implementation of the DataStreamTemplate
 * interface.
 *
 * @author	Arran Jacques
 */

use Monal\Data\Models\DataStreamTemplate;
use Monal\Data\Models\DataSetTemplate;

class MonalDataStreamTemplate implements DataStreamTemplate
{
	/**
	 * The Data Stream Template's messages.
	 *
	 * @var		 Monal\Core\Contracts\MessagesInterface
	 */
	protected $messages;

	/**
	 * The Data Stream Template's ID.
	 *
	 * @var		Integer
	 */
	public $id = null;

	/**
	 * The Data Stream Template's Name.
	 *
	 * @var		String
	 */
	public $name = null;

	/**
	 * A prefix for the table name the Data Stream Template maps to.
	 *
	 * @var		String
	 */
	public $table_prefix = null;

	/**
	 * A collection of Data Set Templates that make up the Data Stream
	 * Template.
	 *
	 * @var		Illuminate\Database\Eloquent\Collection
	 */
	public $data_set_templates;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Monal\Core\Contracts\MessagesInterface');
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
	 * Return the prefix for the table name that the Data Stream Template
	 * maps to.
	 *
	 * @return	String
	 */
	public function tablePrefix()
	{
		return $this->table_prefix;
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
		$this->id = $id;
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
	 * Set a prefix for the table name that the Data Stream Template maps
	 * to.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setTablePrefix($prefix)
	{
		$this->table_prefix = $prefix;
	}

	/**
	 * Add a Data Set Template to the Data Stream Template.
	 *
	 * @param	Monal\Data\Models\DataSetTemplate
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
		// Allow alpha, numeric, hypens, underscores and space characters, and must contain at least 1 alpha character.
		\Validator::extend('stream_data_set_template_name', function($attribute, $value, $parameters)
		{
			return (preg_match('/^[a-z0-9 \-_]+$/i', $value) AND preg_match('/[a-zA-Z]/', $value)) ? true : false;
		});
		$data = array();
		$data['name'] = $this->name;
		$data['table_prefix'] = $this->table_prefix;
		$validation = \Validator::make($data, $validation_rules, $validation_messages);
		if ($validation->passes()) {
			$stream_validates = true;
		} else {
			$stream_validates = false;
			$this->messages->add($validation->messages()->toArray());
		}
		$templates_validate = true;
		$validation_rules = array(
			'name' => 'required|max:100|stream_data_set_template_name',
			'component' => 'required|not_in:0',
		);
		$validation_messages = array(
			'name.required' => 'You need to give this Data Set a Name.',
			'name.max' => 'The Name of this Data Set must be no more than 100 characters long.',
			'name.stream_data_set_template_name' => 'The Name of this Data Set can only contain letters, numbers, spaces, underscores and hyphens, and must contain at least 1 letter.',
			'component.required' => 'You need to set a Component type for this Data Set.',
			'component.not_in' => 'You need to set a Component type for this Data Set.',
		);
		$data_set_template_names = array();
		foreach ($this->data_set_templates as $data_set_template) {
			if (isset($data_set_template_names[\Str::slug($data_set_template->name())])) {
				$stream_validates = false;
				$this->messages->add(
					array(
						'error' => array(
							'You can’t have two Data Sets with the same Name.',
						)
					)
				);
			}
			$data_set_template_names[\Str::slug($data_set_template->name())] = \Str::slug($data_set_template->name());
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

	/**
	 * Return a GUI for the model.
	 *
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function view(array $settings = array())
	{
		$show_validation = isset($settings['show_validation']) ? $settings['show_validation'] : false;
		$name = $this->name();
		$table_prefix = $this->tablePrefix();
		$data_set_templates = $this->dataSetTemplates();
		$messages = $this->messages();
		return \View::make(
			'data::models.data_stream_template',
			compact(
				'messages',
				'name',
				'table_prefix',
				'data_set_templates',
				'show_validation'
			)
		);
	}
}