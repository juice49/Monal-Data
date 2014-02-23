<?php
namespace Fruitful\Data\Contracts;
/**
 * Data Set Templates Interface.
 *
 * A contract for a Data Set Templates library to follow. The Data
 * Set Templates library provides helper functions for working with
 * Data Set Templates.
 *
 * @author	Arran Jacques
 */

interface DataSetTemplatesInterface
{
	/**
	 * Create a new blank Data Set Temaplte model.
	 *
	 * @return	Fruitful\Data\Contracts\DataSetTemplateInterface
	 */
	public function make();

	/**
	 * Sort through input data and identify values that belong to
	 * instances of a Data Set Template. Group the values together and
	 * create a new Data Set Template for each unique Data Set Temaplte
	 * identified.
	 *
	 * @param	Array
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function extractDataSetTemplatesFromInput($input);
}