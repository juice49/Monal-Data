<?php
namespace Fruitful\Data\Repositories\Contracts;
/**
 * Data Set Templates Repository.
 *
 * Repository for storing Data Set Templates. This is a contract for
 * implementations of this repository to follow.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Contracts\DataSetTemplateInterface;

interface DataSetTemplatesRepository
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
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Contracts\DataSetTemplateInterface
	 */
	public function retrieve($key = null);

	/**
	 * Write a Data Set Template model to the repository.
	 *
	 * @param	Fruitful\Data\Contracts\DataSetTemplateInterface
	 * @return	Boolean
	 */
	public function write(DataSetTemplateInterface $data_set_template);
}