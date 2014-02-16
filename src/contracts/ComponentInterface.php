<?php
namespace Fruitful\Data\Contracts;
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
	 * a template for a new implementations of the component.
	 *
	 * @param	Array
	 * @return	Boolean
	 */
	public function templateSettingsValidate(array $settings);

	/**
	 * Construct the template settings for a new component template.
	 *
	 * @param	Array
	 * @return	Array
	 */
	public function constructTemplateSettings(array $settings);

	/**
	 * Return an interface the a user can use to configure settings for
	 * a new or existing component template.
	 *
	 * @param	String
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function templateView($uri, array $settings = array());
}