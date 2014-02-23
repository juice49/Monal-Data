<?php
namespace Fruitful\Data\Libraries;
/**
 * Data Set Template.
 *
 * Implementation of the DataSetTemplateInterface.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Contracts\DataSetTemplateInterface;
use Fruitful\Data\Contracts\ComponentInterface;

class DataSetTemplate implements DataSetTemplateInterface
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
	 * Data Set Template's ID.
	 *
	 * @var		String
	 */
	public $id = null;

	/**
	 * Data Set Template's name.
	 *
	 * @var		String
	 */
	public $name = null;

	/**
	 * An instance of the Component's class that the Data Set Template is
	 * using.
	 *
	 * @var		String
	 */
	protected $component = null;

	/**
	 * Data Set Template's component settings.
	 *
	 * @var		Array
	 */
	protected $component_settings = array();

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Fruitful\Core\Contracts\MessagesInterface');
		$this->components = \App::make('Fruitful\Data\Contracts\ComponentsInterface');
	}

	/**
	 * Return the Data Set Template's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages()
	{
		return $this->messages->get();
	}

	/**
	 * Return the Data Set Template's ID.
	 *
	 * @return	String
	 */
	public function ID()
	{
		return $this->id;
	}

	/**
	 * Return the Data Set Template's Name.
	 *
	 * @return	String
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * Return the Data Set Template's Component's URI.
	 *
	 * @return	String
	 */
	public function componentURI()
	{
		return ($this->component instanceof ComponentInterface) ? $this->component->uri() : null;
	}

	/**
	 * Return the Data Set Template's Component's Name.
	 *
	 * @return	String
	 */
	public function componentName()
	{
		return ($this->component instanceof ComponentInterface) ? $this->component->name() : null;
	}

	/**
	 * Return the Data Set Template's Component's template settings.
	 *
	 * @return	Array
	 */
	public function componentSettings()
	{
		return $this->component_settings;
	}

	/**
	 * Set the ID for the Data Set Template.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setID($id)
	{
		$this->id = is_numeric($id) ? $id : null;
	}

	/**
	 * Set the Name for the Data Set Template.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Set the Component that the Data Set Template will use.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setComponent($component)
	{
		$this->component = $this->components->make($component);
	}

	/**
	 * Set the Data Set Template's Component's template settings.
	 *
	 * @param	Array
	 * @return	Void
	 */
	public function setComponentSettings(array $settings)
	{
		if ($this->component instanceof ComponentInterface) {
			$this->component_settings =  $this->component->constructTemplateSettings($settings);
		} else {
			$this->component_settings = array();
		}
	}

	/**
	 * Check the Data Set Template validates against a set of given
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
		$data['component'] = $this->componentURI();
		$validation = \Validator::make($data, $validation_rules, $validation_messages);
		if ($validation->passes()) {
			$data_validates = true;
		} else {
			$data_validates = false;
			$this->messages->add($validation->messages()->toArray());
		}
		if ($this->component instanceof ComponentInterface) {
			if ($this->component->templateSettingsValidate($this->component_settings)) {
				$component_validates = true;
			} else {
				$component_validates = false;
				$this->messages->add($this->component->messages()->toArray());
			}
		}
		return ($data_validates AND $component_validates) ? true : false;
	}

	/**
	 * Return an interface that a user can use to configure settings for
	 * a new or existing Data Set Template.
	 *
	 * @param	Boolean
	 * @param	Boolean
	 * @return	Illuminate\View\View
	 */
	public function view($component_chooseable = false, $show_validation_messages = false)
	{
		$uri = \Random::letters();
		$name = $this->name;
		$component = $this->componentURI();
		$component_view = ($component) ? $this->component->templateView($uri, $this->componentSettings()) : '';
		$components = array_merge(array(0 => 'Choose component type...'), $this->components->available());
		$messages = ($show_validation_messages) ? $this->messages->get() : false;
		return \View::make(
			'data::data_sets.template',
			compact(
				'messages',
				'uri',
				'name',
				'component',
				'component_view',
				'component_chooseable',
				'components'
			)
		);
	}
}