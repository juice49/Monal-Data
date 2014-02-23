<?php
namespace Fruitful\Data\Repositories\Contracts;
/**
 * Data Sets Repository.
 *
 * Repository for storing Data Sets. This is a contract for
 * implementations of this repository to follow.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Contracts\DataSetInterface;

interface DataSetsRepository
{
	/**
	 * Return the repository's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Retrieve an instance/s from the repository.
	 *
	 * @param	Integer
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Contracts\DataSetInterface
	 */
	public function retrieve($key = null);

	/**
	 * Write a Data Set model to the repository.
	 *
	 * @param	Fruitful\Data\Contracts\DataSetInterface
	 * @return	Boolean
	 */
	public function write(DataSetInterface $data_set);
}