<?php
namespace Fruitful\Data\Libraries;
/**
 * Components.
 *
 * A Components helper library implementing the ComponentsInterface.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Libraries\ComponentsInterface;

class Components implements ComponentsInterface
{
	/**
	 * Return all available component types.
	 *
	 * @return	Array
	 */
	public function available()
	{
		return array(
			'text' => 'Text',
			'wysiwyg' => 'WYSIWYG',
		);
	}

	/**
	 * Return a map of component type uris and their respective classes.
	 *
	 * @return	Array
	 */
	public function resourceMap()
	{
		return array(
			'text' => '\Fruitful\Data\Components\Text',
			'wysiwyg' => '\Fruitful\Data\Components\WYSIWYG',
		);
	}

	/**
	 * Create a new instance of a component type.
	 *
	 * @param	String
	 * @return	Fruitful\Data\Components\ComponentInterface
	 */
	public function make($component_uri)
	{
		$class_map = $this->resourceMap();
		return isset($class_map[$component_uri]) ? new $class_map[$component_uri] : null;
	}
}