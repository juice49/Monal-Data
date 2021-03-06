<?php
namespace Monal\Data\Models;
/**
 * Data Stream Interface.
 *
 * A model for a Data Stream. This is a contract for implementations
 * of this model to follow. The model defines the Name, Stream
 * Template and table details for the Data Stream.
 *
 * @author	Arran Jacques
 */

interface DataStream
{
	/**
	 * Return the Data Stream's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Generate and return a new model of an entry for this stream.
	 *
	 * @return	Monal\Data\Models\DataStreamEntry
	 */
	public function newEntryModel();

	/**
	 * Return the Data Stream's ID.
	 *
	 * @return	Integer
	 */
	public function ID();

	/**
	 * Return the Data Stream's Name.
	 *
	 * @return	String
	 */
	public function name();

	/**
	 * Return the model of the Data Stream Template the Data Stream is
	 * implementing.
	 *
	 * @return	Monal\Data\Models\DataStreamTemplate
	 */
	public function template();

	/**
	 * Return the keys of the Data Sets in the Data Set Template that
	 * will be shown when previewing the entries of this Data Stream.
	 *
	 * @return	Array
	 */
	public function previewColumns();

	/**
	 * Return all of the Data Stream's enteries.
	 *
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function entries();

	/**
	 * Set the ID for the Data Stream.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function setID($id);

	/**
	 * Set the Name for the Data Stream.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setName($name);

	/**
	 * Set the model of the Data Stream Template the Data Stream is
	 * implementing.
	 *
	 * @param	Monal\Data\Models\DataStreamTemplate
	 * @return	Void
	 */
	public function setTemplate(DataStreamTemplate $data_stream_template);

	/**
	 * Define a Data Set to show when previewing entries for the Data
	 * Stream.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function addPreviewColumn($id);

	/**
	 * Discard all of the Preview Columns that have been set.
	 *
	 * @return	Void
	 */
	public function discardPreviewColumns();

	/**
	 * Determine if a Data Set has been defined as a preview column for
	 * the Data Stream.
	 *
	 * @param	Integer
	 * @return	Boolean
	 */
	public function hasPreviewColumn($id);

	/**
	 * Add a new entry to the Data Stream.
	 *
	 * @param	Monal\Data\Models\DataStreamEntry
	 * @return	Boolean
	 */
	public function addEntry(DataStreamEntry $entry);

	/**
	 * Check the Data Stream validates against a set of given rules.
	 *
	 * @param	Array
	 * @param	Array
	 * @return	Boolean
	 */
	public function validates(array $validation_rules = array(), array $validation_messages = array());

	/**
	 * Return a GUI displaying the data stream's settings.
	 *
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function viewSettings(array $settings = array());

	/**
	 * Return a GUI displaying the data stream's enteries.
	 *
	 * @param	Array
	 * @return	Illuminate\View\View
	 */
	public function viewEnteries(array $settings = array());
}