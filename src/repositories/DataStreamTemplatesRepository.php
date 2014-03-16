<?php
namespace Fruitful\Data\Repositories;
/**
 * Data Stream Templates Repository.
 *
 * A repository for storing Data Stream Templates. This is a contract
 * for implementations of this repository to follow. The class
 * defines methods for reading, writing, updating and removing Data
 * Stream Templates to the repository.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Models\DataStreamTemplate;

interface DataStreamTemplatesRepository
{
	/**
	 * Return the repository's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Return a new Data Stream Template model.
	 *
	 * @return	Fruitful\Data\Models\DataStreamTemplate
	 */
	public function newModel();

	/**
	 * Check a Data Stream Template model validates for storage.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Boolean
	 */
	public function validatesForStorage(DataStreamTemplate $data_stream_template);

	/**
	 * Decode a Data Stream Template repository entry into its model
	 * class.
	 *
	 * @param	stdClass
	 * @return	Fruitful\Data\Models\DataStreamTemplate
	 */
	public function decodeFromStorage($results);

	/**
	 * Retrieve an instance/s from the repository.
	 *
	 * @param	Integer
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Models\DataStreamTemplate
	 */
	public function retrieve($key = null);

	/**
	 * Write a Data Stream Template model to the repository.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Boolean
	 */
	public function write(DataStreamTemplate $data_stream_template);
}