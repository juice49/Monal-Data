<?php
namespace Fruitful\Data\Facades;

use Illuminate\Support\Facades\Facade;

class DataSetsHelper extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return	String
	 */
	protected static function getFacadeAccessor() { return 'datasetshelper'; }
}