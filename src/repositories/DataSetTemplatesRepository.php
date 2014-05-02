<?php
namespace Monal\Data\Repositories;
/**
 * Data Set Templates Repository.
 *
 * A repository for storing Data Set Templates. This is a contract
 * for implementations of this repository to follow. The class
 * defines methods for reading, writing, updating and removing Data
 * Set Templates to the repository.
 *
 * @author	Arran Jacques
 */

use Monal\Data\Models\DataSetTemplate;

interface DataSetTemplatesRepository
{
	/**
	 * Return the repository's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Return a new Data Set Template model.
	 *
	 * @return	Monal\Data\Models\DataSetTemplate
	 */
	public function newModel();

	/**
	 * Check the Data Set Template model validates for storage.
	 *
	 * @param	Monal\Data\Models\DataSetTemplate
	 * @return	Boolean
	 */
	public function validatesForStorage(DataSetTemplate $data_set_template);

	/**
	 * Retrieve an instance/s from the repository.
	 *
	 * @param	Integer
	 * @return	Illuminate\Database\Eloquent\Collection / Monal\Data\Models\DataSetTemplate
	 */
	public function retrieve($key = null);

	/**
	 * Write a Data Set Template model to the repository.
	 *
	 * @param	Monal\Data\Models\DataSetTemplate
	 * @return	Boolean
	 */
	public function write(DataSetTemplate $data_set_template);
}