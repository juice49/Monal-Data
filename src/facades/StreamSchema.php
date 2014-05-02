<?php
namespace Monal\Data\Facades;

use Illuminate\Support\Facades\Facade;

class StreamSchema extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return	String
	 */
	protected static function getFacadeAccessor() { return 'monalstreamschema'; }
}