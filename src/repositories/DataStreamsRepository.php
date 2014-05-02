<?php
namespace Monal\Data\Repositories;
/**
 * Data Streams Repository.
 *
 * A repository for storing Data Streams. This is a contract for
 * implementations of this repository to follow. The class defines
 * methods for reading, writing, updating and removing Data Streams
 * to the repository.
 *
 * @author	Arran Jacques
 */

use Monal\Data\Models\DataStream;

interface DataStreamsRepository
{
	/**
	 * Return the repository's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Return a new Data Stream model.
	 *
	 * @return	Monal\Data\Models\DataStream
	 */
	public function newModel();

	/**
	 * Check a Data Stream model validates for storage.
	 *
	 * @param	Monal\Data\Models\DataStream
	 * @return	Boolean
	 */
	public function validatesForStorage(DataStream $data_stream);

	/**
	 * Decode a Data Stream repository entry into its model class.
	 *
	 * @param	stdClass
	 * @return	Monal\Data\Models\DataStream
	 */
	public function decodeFromStorage($result);

	/**
	 * Retrieve an instance/s from the repository.
	 *
	 * @param	Integer
	 * @return	Illuminate\Database\Eloquent\Collection / Monal\Data\Models\DataStream
	 */
	public function retrieve($key = null);

	/**
	 * Write a Data Stream model to the repository.
	 *
	 * @param	Monal\Data\Models\DataStream
	 * @return	Boolean
	 */
	public function write(DataStream $data_stream);
}