<?php
namespace Monal\Data\Facades;

use Illuminate\Support\Facades\Facade;

class DataStreamTemplatesRepository extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return	String
	 */
	protected static function getFacadeAccessor() { return 'datastreamtemplatesrepository'; }
}