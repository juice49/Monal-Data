<?php
namespace Fruitful\Data\Repositories;
/**
 * Data Sets Repository.
 *
 * Repository for storing Data Sets. This is a contract for
 * implementations of this repository to follow.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Models\DataSet;

interface DataSetsRepository
{
	/**
	 * Return the repository's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Return a new Data Set model.
	 *
	 * @return	Fruitful\Data\Models\DataSet
	 */
	public function newModel();

	/**
	 * Check a Data Set model validates for storage.
	 *
	 * @param	Fruitful\Data\Models\DataSet
	 * @return	Boolean
	 */
	public function validatesForStorage(DataSet $data_set);

	/**
	 * Retrieve an instance/s from the repository.
	 *
	 * @param	Integer
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Models\DataSet
	 */
	public function retrieve($key = null);

	/**
	 * Write a Data Set model to the repository.
	 *
	 * @param	Fruitful\Data\Models\DataSet
	 * @return	Boolean
	 */
	public function write(DataSet $data_set);
}