<?php
namespace Fruitful\Data\Models;
/**
 * Data Set Interface.
 *
 * A model for a Data Set. This is a contract for implementations of
 * this model to follow. The model defines the Name, Component,
 * Component settings and Component values for the Data Set.
 *
 * @author	Arran Jacques
 */

interface DataSet
{
	/**
	 * Return the Data Set's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Return the Data Set's ID.
	 *
	 * @return	Integer
	 */
	public function ID();

	/**
	 * Return the Data Set's Name.
	 *
	 * @return	String
	 */
	public function name();

	/**
	 * Return the ID of the Data Set Template the Data Set is implementing.
	 *
	 * @return	String
	 */
	public function templateID();

	/**
	 * Return the Data Set's Component's URI.
	 *
	 * @return	String
	 */
	public function componentURI();

	/**
	 * Return the Data Set's Component's Name.
	 *
	 * @return	String
	 */
	public function componentName();

	/**
	 * Return the Data Set's Component's settings.
	 *
	 * @return	Array
	 */
	public function componentSettings();

	/**
	 * Return the Data Set's Component's values.
	 *
	 * @return	Array
	 */
	public function componentValues();

	/**
	 * Set the ID for the Data Set.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function setID($id);

	/**
	 * Set the Name for the Data Set.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setName($name);

	/**
	 * Set the ID for the Data Set Template that the Data Set is
	 * implementing.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function setTemplateID($id);

	/**
	 * Set the Component that the Data Set will use.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setComponent($component);

	/**
	 * Set the Data Set's Component's settings.
	 *
	 * @param	Array
	 * @return	Void
	 */
	public function setComponentSettings(array $settings);

	/**
	 * Set the Data Set's Component's values.
	 *
	 * @param	Array
	 * @return	Void
	 */
	public function setComponentValues(array $values);

	/**
	 * Check if the Data Set has been set a Component type.
	 *
	 * @return	Boolean
	 */
	public function hasComponent();

	/**
	 * Check the Data Set validates against a set of given rules.
	 *
	 * @param	Array
	 * @param	Array
	 * @return	Boolean
	 */
	public function validates(array $validation_rules = array(), array $validation_messages = array());

	/**
	 * Return the Data Set's interface.
	 *
	 * @param	Boolean
	 * @return	Illuminate\View\View
	 */
	public function view($show_validation_messages = false);
}