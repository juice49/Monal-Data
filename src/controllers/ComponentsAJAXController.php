<?php
/**
 * Components AJAX Controller.
 *
 * Controller for AJAX HTTP requests related to Components. Acts as a
 * mediator between incoming AJAX HTTP requests and the application.
 * Receives the AJAX HTTP requests and runs the appropriate
 * application layer logic, returning the results in an appropriate
 * format.
 *
 * @author	Arran Jacques
 */

class ComponentsAJAXController extends BaseController
{
	/**
	 * An instance of the Components library. 
	 *
	 * @var		 Fruitful\Data\Libraries\ComponentsInterface
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
		$this->components = \App::make('Fruitful\Data\Libraries\ComponentsInterface');
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