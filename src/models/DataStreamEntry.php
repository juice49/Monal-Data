<?php
namespace Fruitful\Data\Models;
/**
 * Data Stream Entry.
 *
 * A model for a Data Stream Entry. This is a contract for
 * implementations of this model to follow. The model defines the
 * structure of an entry for a Data Stream, including what Data Sets
 * the entry has. The Data Sets the entry has will depend on the
 * Data Stream Template the Data Stream is using to model it’s
 * entries
 *
 * @author	Arran Jacques
 */

interface DataStreamEntry
{
	/**
	 * Use a Data Set Template to set what Data Sets the model has.
	 *
	 * @param	Fruitful\Data\Models\DataStreamTemplate
	 * @return	Void
	 */
	public function buildModelFromDataStreamTemplate(DataStreamTemplate $data_stream_template);

	/**
	 * Return the Data Sets the Entry has available.
	 *
	 * @return	Array
	 */
	public function dataSets();

	/**
	 * Return the Entry's interface.
	 *
	 * @param	Boolean
	 * @return	Illuminate\View\View
	 */
	public function view($show_validation_messages = false);
}