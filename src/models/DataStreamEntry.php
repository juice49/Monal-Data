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

use Fruitful\Data\Models\DataSet;

interface DataStreamEntry
{
	/**
	 * Return the model's Data Sets.
	 *
	 * @return	Array
	 */
	public function dataSets();

	/**
	 * Add a new Data Set to the model.
	 *
	 * @param	Fruitful\Data\Models\DataSet
	 * @return	Void
	 */
	public function addDataSet(DataSet $data_set);

	/**
	 * Return a view of the model.
	 *
	 * @param	Boolean
	 * @return	Illuminate\View\View
	 */
	public function view($show_validation_messages = false);
}