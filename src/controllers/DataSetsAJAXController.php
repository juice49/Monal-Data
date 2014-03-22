<?php
/**
 * Data Sets AJAX Controller.
 *
 * Controller for AJAX HTTP requests related to Data Sets. Acts as a
 * mediator between incoming AJAX HTTP requests and the application.
 * Receives the AJAX HTTP requests and runs the appropriate
 * application layer logic, returning the results in an appropriate
 * format.
 *
 * @author	Arran Jacques
 */

class DataSetsAJAXController extends BaseController
{
	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		parent::__construct(\App::make('Fruitful\Core\Contracts\GatewayInterface'));
	}

	/**
	 * Create a new Data Set Template and return it's view.
	 *
	 * @param	Array
	 * @return	JSON
	 */
	public function dataSetTemplateView($data)
	{
		$results = array();
		$data_set_template = \App::make('Fruitful\Data\Models\DataSetTemplate');
		$results['status'] = 'OK';
		$results['view'] = $data_set_template->view(true, true)->render();
		return json_encode($results, JSON_FORCE_OBJECT);
	}
}