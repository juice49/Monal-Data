<?php
namespace Monal\Data\Libraries;
/**
 * Components Interface.
 *
 * A library of general/helper methods for working with Components.
 * This is a contract for implementations of this library to follow.
 *
 * @author	Arran Jacques
 */

interface ComponentsInterface
{
	/**
	 * Return all available component types.
	 *
	 * @return	Array
	 */
	public function available();

	/**
	 * Return a map of component type uris and their respective classes.
	 *
	 * @return	Array
	 */
	public function resourceMap();

	/**
	 * Create a new instance of a component type.
	 *
	 * @param	String
	 * @return	Monal\Data\Components\ComponentInterface
	 */
	public function make($component_uri);
}