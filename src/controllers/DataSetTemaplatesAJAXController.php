<?php
/**
 * Data Set Temaplates AJAX Controller.
 *
 * Controller for AJAX HTTP requests related the Data Set Templates.
 * Acts as a mediator between incoming AJAX HTTP requests and the
 * application. Receives the AJAX HTTP requests and runs the
 * appropriate application layer logic, returning the results in an
 * appropriate format.
 *
 * @author	Arran Jacques
 */

use Fruitful\Core\Contracts\GatewayInterface;
use Fruitful\Data\Contracts\ComponentsInterface;

class DataSetTemaplatesAJAXController extends BaseController {

	/**
	 * Instance of class implementing DataSetTemplatesInterface.
	 *
	 * @var		 Fruitful\Data\Contracts\DataSetTemplatesInterface
	 */
	protected $data_set_templates;

	/**
	 * Instance of class implementing ComponentsInterface.
	 *
	 * @var		 Fruitful\Data\Contracts\ComponentsInterface
	 */
	protected $components;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		parent::__construct(\App::make('Fruitful\Core\Contracts\GatewayInterface'));
		$this->data_set_templates = \App::make('Fruitful\Data\Contracts\DataSetTemplatesInterface');
		$this->components = \App::make('Fruitful\Data\Contracts\ComponentsInterface');
	}

	/**
	 * Create a new instance of a Component type and generate an
	 * interface that can be used to build a template for the Component.
	 *
	 * @param	Array
	 * @return	JSON
	 */
	public function componentTemplate($data)
	{
		$results = array();
		if (isset($data['type']) AND isset($data['uri']))
		{
			$component = $this->components->make($data['type']);
			$results['status'] = 'OK';
			$results['view'] = $component->templateView($data['uri'], array())->render();
		}
		return json_encode($results, JSON_FORCE_OBJECT);
	}
}