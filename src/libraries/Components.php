<?php
namespace Fruitful\Data\Libraries;
/**
 * Components.
 *
 * Implementation of the ComponentsInterface.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Contracts\ComponentsInterface;

class Components implements ComponentsInterface
{
	/**
	 * Return all available component types.
	 *
	 * @return	Array
	 */
	public function available()
	{
		return array('wysiwyg' => 'WYSIWYG');
	}

	/**
	 * Return a map of component type uris and their respective classes.
	 *
	 * @return	Array
	 */
	public function resourceMap()
	{
		return array('wysiwyg' => '\Fruitful\Data\Components\WYSIWYG');
	}

	/**
	 * Create a new instance of a component type.
	 *
	 * @param	String
	 * @return	Fruitful\Data\Contracts\ComponentInterface
	 */
	public function make($component_uri)
	{
		$class_map = $this->resourceMap();
		return isset($class_map[$component_uri]) ? new $class_map[$component_uri] : null;
	}
}