<?php
namespace Fruitful\Data\Contracts;
/**
 * Data Sets Interface.
 *
 * A contract for a Data Set library to follow. The Data Sets library
 * provides helper functions for working with Data Sets.
 *
 * @author	Arran Jacques
 */

interface DataSetsInterface
{
	/**
	 * Create a new blank Data Set model.
	 *
	 * @return	Fruitful\Data\Contracts\DataSetInterface
	 */
	public function make();

	/**
	 * Sort through input data and identify values that belong to
	 * instances of a Data Setm and Group the values together.
	 *
	 * @param	Array
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function extractDataSetValuesFromInput(array $input);
}