<?php
namespace Monal\Data\Models;
/**
 * Monal Data Set Template.
 *
 * The Monal System's implementation of the DataSetTemplate
 * interface.
 *
 * @author	Arran Jacques
 */

use Monal\Data\Models\DataSetTemplate;
use Monal\Data\Components\ComponentInterface;

class MonalDataSetTemplate implements DataSetTemplate
{
	/**
	 * The Data Set Template's messages.
	 *
	 * @var		 Monal\Core\Contracts\MessagesInterface
	 */
	protected $messages;

	/**
	 * An instance of the Components library. 
	 *
	 * @var		 Monal\Data\Libraries\ComponentsInterface
	 */
	protected $components;

	/**
	 * The Data Set Template's ID.
	 *
	 * @var		String
	 */
	public $id = null;

	/**
	 * The Data Set Template's URI.
	 *
	 * @var		String
	 */
	protected $uri = null;

	/**
	 * The Data Set Template's Name.
	 *
	 * @var		String
	 */
	public $name = null;

	/**
	 * An instance of the Component the Data Set Template is using.
	 *
	 * @var		Monal\Data\Components\ComponentInterface
	 */
	protected $component = null;

	/**
	 * The Data Set Template's Component's settings.
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
		$this->messages = \App::make('Monal\Core\Contracts\MessagesInterface');
		$this->components = \App::make('Monal\Data\Libraries\ComponentsInterface');
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
	 * Return the Data Set Template's URI.
	 *
	 * @return	String
	 */
	public function URI()
	{
		return $this->uri;
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
		$this->id = (integer) $id;
	}

	/**
	 * Set the URI for the Data Set Template.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setURI($uri)
	{
		$this->uri = (string) $uri;
	}

	/**
	 * Set the Name for the Data Set Template.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
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
		if ($this->hasComponent()) {
			$this->component_settings =  $this->component->constructTemplateSettings($settings);
		} else {
			$this->component_settings = array();
		}
	}

	/**
	 * Check if the Data Set Template has been set a Component type.
	 *
	 * @return	Boolean
	 */
	public function hasComponent()
	{
		return ($this->component instanceof ComponentInterface) ? true : false;
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
			$template_validates = true;
		} else {
			$template_validates = false;
			$this->messages->add($validation->messages()->toArray());
		}
		if ($this->hasComponent()) {
			if ($this->component->templateSettingsValidate($this->component_settings)) {
				$component_validates = true;
			} else {
				$component_validates = false;
				$this->messages->add($this->component->messages()->toArray());
			}
		}
		return ($template_validates AND $component_validates) ? true : false;
	}

	/**
	 * Return a GUI for the model.
	 *
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function view(array $settings = array())
	{
		$uri = $this->uri ? $this->uri : \Random::letters();
		$name = $this->name;
		$component = $this->componentURI();
		$component_view = ($component) ? $this->component->templateView($uri, $this->componentSettings()) : '';
		$components = array_merge(array(0 => 'Choose component type...'), $this->components->available());

		$show_validation = isset($settings['show_validation']) ? $settings['show_validation'] : false;
		$choose_component = isset($settings['choose_component']) ? $settings['choose_component'] : false;
		$removable = isset($settings['removable']) ? $settings['removable'] : false;

		$messages = ($show_validation) ? $this->messages->get() : false;
		return \View::make(
			'data::models.data_set_template',
			compact(
				'messages',
				'uri',
				'name',
				'component',
				'component_view',
				'components',
				'choose_component',
				'removable'
			)
		);
	}
}