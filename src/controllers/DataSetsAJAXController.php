<?php
/**
 * Data Sets AJAX Controller.
 *
 * Controller for AJAX HTTP requests related the Data Sets. Acts as a
 * mediator between incoming AJAX HTTP requests and the application.
 * Receives the AJAX HTTP requests and runs the appropriate
 * application layer logic, returning the results in an appropriate
 * format.
 *
 * @author	Arran Jacques
 */

use Fruitful\Core\Contracts\GatewayInterface;
use Fruitful\Data\Contracts\ComponentsInterface;

class DataSetsAJAXController extends BaseController {

	/**
	 * Instance of class implementing DataSetTemplatesInterface.
	 *
	 * @var		 Fruitful\Data\Contracts\DataSetTemplatesInterface
	 */
	protected $data_set_templates;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		parent::__construct(\App::make('Fruitful\Core\Contracts\GatewayInterface'));
		$this->data_set_templates = \App::make('Fruitful\Data\Contracts\DataSetTemplatesInterface');
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
		$data_set_template = $this->data_set_templates->make();
		$results['status'] = 'OK';
		$results['view'] = $data_set_template->view(true)->render();
		return json_encode($results, JSON_FORCE_OBJECT);
	}
}