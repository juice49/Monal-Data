<?php
namespace Fruitful\Data\Libraries;
/**
 * Data Set Templates Interface.
 *
 * A library of general/helper methods for working with Data Set
 * Templates. This is a contract for implementations of this library
 * to follow.
 *
 * @author	Arran Jacques
 */

interface DataSetTemplatesInterface
{
	/**
	 * Sort through input data and identify values that belong to
	 * instances of a Data Set Template. Group the values together and
	 * create a new Data Set Template for each unique Data Set Template
	 * identified.
	 *
	 * @param	Array
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function extractDataSetTemplatesFromInput(array $input);
}