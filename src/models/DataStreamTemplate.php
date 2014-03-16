<?php
namespace Fruitful\Data\Models;
/**
 * Data Stream Template Interface.
 *
 * A model for a Data Stream Template. This is a contract for
 * implementations of this model to follow. The model defines the
 * Name and Data Sets that a Data Stream will inherit if it implements
 * this Data Stream Template.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Models\DataSetTemplate;

interface DataStreamTemplate
{
	/**
	 * Return the Data Stream Template's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages();

	/**
	 * Return the Data Stream Template's ID.
	 *
	 * @return	Integer
	 */
	public function ID();

	/**
	 * Return the Data Stream Template's Name.
	 *
	 * @return	String
	 */
	public function name();

	/**
	 * Return the collection of Data Set Templates attached to the Data
	 * Stream Template. 
	 *
	 * @return	Illuminate\Database\Eloquent\Collection
	 */
	public function dataSetTemplates();

	/**
	 * Set the ID for the Data Stream Template.
	 *
	 * @param	Integer
	 * @return	Void
	 */
	public function setID($id);

	/**
	 * Set the Name for the Data Stream Template.
	 *
	 * @param	String
	 * @return	Void
	 */
	public function setName($name);

	/**
	 * Add a Data Set Template to the Data Stream Template.
	 *
	 * @param	Fruitful\Data\Libraries\DataSetTemplate
	 * @return	Void
	 */
	public function addDataSetTemplate(DataSetTemplate $data_set_template);

	/**
	 * Discard all of the Data Set Templates attached to the Data Stream
	 * Template.
	 *
	 * @return	Void
	 */
	public function discardDataSetTemplates();

	/**
	 * Check the Data Stream Template validates against a set of given
	 * rules.
	 *
	 * @param	Array
	 * @param	Array
	 * @return	Boolean
	 */
	public function validates(array $validation_rules = array(), array $validation_messages = array());
}