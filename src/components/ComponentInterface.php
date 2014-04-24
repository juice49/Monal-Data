<?php
namespace Fruitful\Data\Components;
/**
 * Component Interface.
 *
 * A contract for Components to follow. A component provides a
 * specific piece of functionality, for example to upload an image. A
 * Component class should provide functions for generating templates,
 * and implementations of those templates.
 *
 * @author	Arran Jacques
 */

interface ComponentInterface
{
	/**
	 * Return the Component's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Return the Component's URI.
	 *
	 * @return	String
	 */
	public function uri();

	/**
	 * Return the Component's name.
	 *
	 * @return	String
	 */
	public function name();

	/**
	 * Check an array of settings validate and are suitable to be used as
	 * a template for a new implementations of the Component.
	 *
	 * @param	Array
	 * @return	Boolean
	 */
	public function templateSettingsValidate(array $settings);

	/**
	 * Construct a set of template settings for the Component.
	 *
	 * @param	Array
	 * @return	Array
	 */
	public function constructTemplateSettings(array $settings);

	/**
	 * Return an interface that a user can use to configure settings for
	 * a new or existing Component template.
	 *
	 * @param	String
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function templateView($uri, array $settings = array());

	/**
	 * Check an array of implementation values validate against a set of
	 * template settings.
	 *
	 * @param	Array
	 * @param	Array
	 * @return	Boolean
	 */
	public function implementationValuesValidate(array $values = array(), array $settings = array());

	/**
	 * Return a summary for a set of valid component values.
	 *
	 * @param	Array
	 * @param	String
	 */
	public function summariseValues(array $values = array());

	/**
	 * Convert a set of valid component values into as simple a format as
	 * possible for easy storage.
	 *
	 * @param	Array
	 * @return	Mixed
	 */
	public function stripImplementationValues(array $values = array());

	/**
	 * Convert a set of simplified values into a more complex array of
	 * values.
	 *
	 * @param	Mixed
	 * @return	Array
	 */
	public function dressImplementationValues($values);

	/**
	 * Return an interface that lets a user use an implementation of the
	 * Component.
	 *
	 * @param	String
	 * @param	Array
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function implementationView($uri, array $settings = array(), array $values = array());
}