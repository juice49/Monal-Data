<?php
namespace Monal\Data\Models;
/**
 * Data Set Template Interface.
 *
 * A model for a Data Set Template. This is a contract for
 * implementations of this model to follow. The model defines the
 * Name, Component and Component settings that a Data Set will
 * inherit if it implements this Data Set Template.
 *
 * @author	Arran Jacques
 */

interface DataSetTemplate
{
	/**
	 * Return the Data Set Template's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Return the Data Set Template's ID.
	 *
	 * @return	String
	 */
	public function ID();

	/**
	 * Return the Data Set Template's URI.
	 *
	 * @return	String
	 */
	public function URI();

	/**
	 * Return the Data Set Template's Name.
	 *
	 * @return	String
	 */
	public function name();

	/**
	 * Return the Data Set Template's Component's URI.
	 *
	 * @return	String
	 */
	public function componentURI();

	/**
	 * Return the Data Set Template's Component's Name.
	 *
	 * @return	String
	 */
	public function componentName();

	/**
	 * Return the Data Set Template's Component's template settings.
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
	 * Set the URI for the Data Set Template.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setURI($uri);

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
	 * Set the Data Set Template's Component's template settings.
	 *
	 * @param	Array
	 * @return	Void
	 */
	public function setComponentSettings(array $settings);

	/**
	 * Check if the Data Set Template has been set a Component type.
	 *
	 * @return	Boolean
	 */
	public function hasComponent();

	/**
	 * Generate a new data set model based on this data set template.
	 *
	 * @return	Monal\Pages\Models\MonalPage
	 */
	public function newDataSetFromTemplate();

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
	 * Return a GUI for the model.
	 *
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function view(array $settings = array());
}