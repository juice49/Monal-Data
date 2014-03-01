<?php
namespace Fruitful\Data\Libraries;
/**
 * Data Sets Interface.
 *
 * A library of general/helper methods for working with Data Sets.
 * This is a contract for implementations of this library to follow.
 *
 * @author	Arran Jacques
 */

interface DataSetsInterface
{
	/**
	 * Sort through input data and identify values that belong to
	 * instances of a Data Sets and Group the values together.
	 *
	 * @param	Array
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function extractDataSetValuesFromInput(array $input);
}