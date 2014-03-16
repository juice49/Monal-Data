<?php
namespace Fruitful\Data\Models;
/**
 * Data Set.
 *
 * The Fruitful System's implementation of the DataSet interface.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Models\DataSet;
use Fruitful\Data\Components\ComponentInterface;

class FruitfulDataSet implements DataSet
{
	/**
	 * The Data Set's messages.
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
	 * The Data Set's ID.
	 *
	 * @var		Integer
	 */
	protected $id = null;

	/**
	 * The Data Set's Name.
	 *
	 * @var		String
	 */
	protected $name = null;

	/**
	 * The ID of the Data Set Template the Data Set is implementing.
	 *
	 * @var		Integer
	 */
	protected $template_id = null;

	/**
	 * An instance of the Component the Data Set Template is using.
	 *
	 * @var		Fruitful\Data\Components\ComponentInterface
	 */
	protected $component = null;

	/**
	 * The Data Set Template's Component's settings.
	 *
	 * @var		Array
	 */
	protected $component_settings = array();

	/**
	 * The Data Set Template's Component's values.
	 *
	 * @var		Array
	 */
	protected $component_values = array();

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Fruitful\Core\Contracts\MessagesInterface');
		$this->components = \App::make('Fruitful\Data\Libraries\ComponentsInterface');
	}

	/**
	 * Return the Data Set's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages()
	{
		return $this->messages->get();
	}

	/**
	 * Return the Data Set's ID.
	 *
	 * @return	Integer
	 */
	public function ID()
	{
		return $this->id;
	}

	/**
	 * Return the Data Set's Name.
	 *
	 * @return	String
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * Return the ID of the Data Set Template the Data Set is implementing.
	 *
	 * @return	Integer
	 */
	public function templateID()
	{
		return $this->template_id;
	}

	/**
	 * Return the Data Set's Component's URI.
	 *
	 * @return	String
	 */
	public function componentURI()
	{
		return ($this->hasComponent()) ? $this->component->uri() : null;
	}

	/**
	 * Return the Data Set's Component's Name.
	 *
	 * @return	String
	 */
	public function componentName()
	{
		return ($this->hasComponent()) ? $this->component->name() : null;
	}

	/**
	 * Return the Data Set's Component's settings.
	 *
	 * @return	Array
	 */
	public function componentSettings()
	{
		return $this->component_settings;
	}

	/**
	 * Return the Data Set's Component's values.
	 *
	 * @return	Array
	 */
	public function componentValues()
	{
		return $this->component_values;
	}

	/**
	 * Set the ID for the Data Set.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function setID($id)
	{
		$this->id = (integer) $id;
	}

	/**
	 * Set the Name for the Data Set.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Set the ID for the Data Set Template that the Data Set is
	 * implementing.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function setTemplateID($id)
	{
		$this->template_id = $id;
	}

	/**
	 * Set the Component that the Data Set will use.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setComponent($component)
	{
		$this->component = $this->components->make($component);
	}

	/**
	 * Set the Data Set's Component's settings.
	 *
	 * @param	Array
	 * @return	Void
	 */
	public function setComponentSettings(array $settings)
	{
		$this->component_settings = $settings;
	}

	/**
	 * Set the Data Set's Component's values.
	 *
	 * @param	Array
	 * @return	Void
	 */
	public function setComponentValues(array $values)
	{
		$this->component_values = $values;
	}

	/**
	 * Check if the Data Set has been set a Component type.
	 *
	 * @return	Boolean
	 */
	public function hasComponent()
	{
		return ($this->component instanceof ComponentInterface) ? true : false;
	}

	/**
	 * Check the Data Set validates against a set of given rules.
	 *
	 * @param	Array
	 * @param	Array
	 * @return	Boolean
	 */
	public function validates(array $validation_rules = array(), array $validation_messages = array())
	{
		$data = array(
			'name' => $this->name(),
			'template_id' => $this->templateID(),
			'component' => $this->componentURI(),
		);
		$validation = \Validator::make($data, $validation_rules, $validation_messages);
		if ($validation->passes()) {
			$data_set_validates = true;
		} else {
			$data_set_validates = false;
			$this->messages->add($validation->messages()->toArray());
		}
		if ($this->hasComponent()) {
			if ($this->component->implementationValuesValidate($this->component_values, $this->component_settings)) {
				$component_validates = true;
			} else {
				$component_validates = false;
				$this->messages->add($this->component->messages()->toArray());
			}
		}
		return ($data_set_validates AND $component_validates) ? true : false;
	}

	/**
	 * Return the Data Set's interface.
	 *
	 * @param	Boolean
	 * @return	Illuminate\View\View
	 */
	public function view($show_validation_messages = false)
	{
		$uri = \Random::letters();
		$name = $this->name();
		if ($this->hasComponent()) {
			$component_view = $this->component->implementationView($uri, $this->componentSettings(), $this->componentValues());
		} else {
			$component_view = '';
		}
		$messages = ($show_validation_messages) ? $this->messages->get() : false;
		return \View::make(
			'data::data_sets.implementation',
			compact(
				'messages',
				'uri',
				'name',
				'component_view'
			)
		);
	}
}