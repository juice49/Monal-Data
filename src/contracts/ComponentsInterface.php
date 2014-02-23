<?php
namespace Fruitful\Data\Contracts;
/**
 * Components Interface.
 *
 * A contract for a Components library to follow. The Components
 * library provides helper functions for working with Components.
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
	 * @return	Fruitful\Data\Contracts\ComponentInterface
	 */
	public function make($component_uri);
}