<?php
namespace Fruitful\Data\Contracts;
/**
 * Data Set Template Interface.
 *
 * A model for a Data Set Template. This is a contract for
 * implementations of this model to follow. The model defines the
 * name, Component and component settings that a Data Set will
 * inherit if it implements this Data Set Template.
 *
 * @author	Arran Jacques
 */

interface DataSetTemplateInterface
{
	/**
	 * Return the Data Set Template's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Return the Data Set Template’s ID.
	 *
	 * @return	String
	 */
	public function ID();

	/**
	 * Return the Data Set Template’s Name.
	 *
	 * @return	String
	 */
	public function name();

	/**
	 * Return the Data Set Template’s Component’s URI.
	 *
	 * @return	String
	 */
	public function componentURI();

	/**
	 * Return the Data Set Template’s Component’s Name.
	 *
	 * @return	String
	 */
	public function componentName();

	/**
	 * Return the Data Set Template’s Component’s template settings.
	 *
	 * @return	Array
	 */
	public function componentSettings();

	/**
	 * Set the ID for the Data Set Template.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setID($id);

	/**
	 * Set the Name for the Data Set Template.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setName($name);

	/**
	 * Set the Component that the Data Set Template will use.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setComponent($component);

	/**
	 * Set the Data Set Template’s Component’s template settings.
	 *
	 * @param	Array
	 * @return	Void
	 */
	public function setComponentSettings(array $settings);

	/**
	 * Check the Data Set Template validates against a set of given
	 * rules.
	 *
	 * @param	Array
	 * @param	Array
	 * @return	Boolean
	 */
	public function validates(array $validation_rules = array(), array $validation_messages = array());

	/**
	 * Return an interface that a user can use to configure settings for
	 * a new or existing Data Set Template.
	 *
	 * @param	Boolean
	 * @param	Boolean
	 * @return	Illuminate\View\View
	 */
	public function view($component_chooseable = false, $show_validation_messages = false);
}