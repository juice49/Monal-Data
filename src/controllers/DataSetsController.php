<?php
/**
 * Data Sets Controller.
 *
 * Controller for Data Sets Admin pages. Acts as a mediator between
 * incoming HTTP requests and the application. Receives the HTTP
 * requests and runs the appropriate application layer logic,
 * outputting the results to a user interface.
 *
 * @author	Arran Jacques
 */

use Fruitful\GatewayInterface;
use Fruitful\Data\Libraries\ComponentsInterface;
use Fruitful\Data\Repositories\DataSetsRepository;
use Fruitful\Data\Repositories\DataSetTemplatesRepository;

class DataSetsController extends AdminController
{
	/**
	 * An instance of the Components library. 
	 *
	 * @var		 Fruitful\Data\Libraries\ComponentsInterface
	 */
	protected $components;

	/**
	 * An instance the of the Data Sets Repository.
	 *
	 * @var		 Fruitful\Data\Repositories\DataSetsRepository
	 */
	protected $data_sets_repo;

	/**
	 * An instance the of the Data Set Templates Repository.
	 *
	 * @var		 Fruitful\Data\Repositories\DataSetTemplatesRepository
	 */
	protected $data_set_templates_repo;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct(
		GatewayInterface $system_gateway,
		ComponentsInterface $components,
		DataSetsRepository $data_sets_repo,
		DataSetTemplatesRepository $data_set_templates_repo
		) {
		parent::__construct($system_gateway);
		$this->components = $components;
		$this->data_sets_repo = $data_sets_repo;
		$this->data_set_templates_repo = $data_set_templates_repo;
	}

	/**
	 * Mediate HTTP requests to retrieve Data Sets and output the
	 * results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function dataSets()
	{
		if (!$this->system->user->hasAdminPermissions('data_sets')) {
			return Redirect::route('admin.dashboard');
		}
		$data_sets = $this->data_sets_repo->retrieve();
		$messages = $this->system->messages->get();
		return View::make('data::data_set_implementations.data_set_implementations', compact('messages', 'data_sets'));
	}

	/**
	 * Mediate HTTP requests to create new Data Sets and output the
	 * results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function chooseTemplate()
	{
		if (!$this->system->user->hasAdminPermissions('data_sets', 'create_data_set')) {
			return Redirect::route('admin.data-sets');
		}
		if ($this->input) {
			$validation = Validator::make(
				$this->input,
				array(
					'data_set_template' => 'required|not_in:0',
				),
				array(
					'data_set_template.required' => 'You need to choose a Data Set Template to use for this Data Set.',
					'data_set_template.not_in' => 'You need to choose a Data Set Template to use for this Data Set.',
				)
			);
			if ($validation->passes()) {
				return Redirect::route('admin.data-sets.create', $this->input['data_set_template']);
			}
			$this->system->messages->add($validation->messages()->toArray());
		}
		$data_set_templates = array();
		foreach ($this->data_set_templates_repo->retrieve() as $data_set_template) {
			$data_set_templates[$data_set_template->ID()] = $data_set_template->name();
		}
		$data_set_templates = array(0 => 'Choose template...') + $data_set_templates;
		$messages = $this->system->messages->get();
		return View::make(
			'data::data_set_implementations.choose_data_set_template',
			compact('messages', 'data_set_templates')
		);
	}

	/**
	 * Mediate HTTP requests to create new Data Sets and output the
	 * results.
	 *
	 * @param	Integer
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function createDataSet($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_sets', 'create_data_set')) {
			return Redirect::route('admin.data-sets');
		}
		if ($data_set_template = $this->data_set_templates_repo->retrieve($id)) {
			$data_set = $this->data_sets_repo->newModel();
			$data_set->setTemplateID($id);
			$data_set->setComponent($data_set_template->componentURI());
			$data_set->setComponentSettings($data_set_template->componentSettings());
			if ($this->input) {
				$data_set_values = \DataSetsHelper::extractDataSetValuesFromInput($this->input)->first();
				$data_set->setName($data_set_values['name']);
				$data_set->setComponentValues($data_set_values['component_values']);
				if ($this->data_sets_repo->write($data_set)) {
					$this->system->messages->add(
						array(
							'success' => array(
								'You successfully created the Data Set "' . $data_set->name() . '".',
							)
						)
					)->flash();
					return Redirect::route('admin.data-sets');
				}
				$this->system->messages->add($this->data_sets_repo->messages()->toArray());
			}
			$messages = $this->system->messages->get();
			return View::make(
				'data::data_set_implementations.create_data_set_implementation',
				compact('messages', 'data_set')
			);
		}
		return Redirect::route('admin.data-sets');
	}

	/**
	 * Mediate HTTP requests to update an existing Data Set Template and
	 * output the results.
	 *
	 * @param	Integer
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function editDataSet($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_sets', 'edit_data_set')) {
			return Redirect::route('admin.data-sets');
		}
		if ($data_set = $this->data_sets_repo->retrieve($id)) {
			if (!$data_set->hasComponent()) {
				$this->system->messages->add(
					array(
						'error' => array(
							'The Data Set Template for this Data Set does not exist or has been deleted.',
						)
					)
				);
			} else {
				if ($this->input) {
					$data_set_values = \DataSetsHelper::extractDataSetValuesFromInput($this->input)->first();
					$data_set->setName($data_set_values['name']);
					$data_set->setComponentValues($data_set_values['component_values']);
					if ($this->data_sets_repo->write($data_set)) {
						$this->system->messages->add(
							array(
								'success' => array(
									'You successfully updated the Data Set "' . $data_set->name() . '".',
								)
							)
						)->flash();
						return Redirect::route('admin.data-sets');
					}
					$this->system->messages->add($this->data_sets_repo->messages()->toArray());
				}
			}
			$messages = $this->system->messages->get();
			return View::make(
				'data::data_set_implementations.edit_data_set_implementation',
				compact('messages', 'data_set')
			);
		}
		return Redirect::route('admin.data-sets');
	}

	/**
	 * Mediate HTTP requests to retrieve Data Set Templates and output
	 * the results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function dataSetTemplates()
	{
		if (!$this->system->user->hasAdminPermissions('data_set_templates')) {
			return Redirect::route('admin.dashboard');
		}
		$data_set_templates = $this->data_set_templates_repo->retrieve();
		$messages = $this->system->messages->get();
		return View::make(
			'data::data_set_templates.data_set_templates',
			compact('messages', 'data_set_templates')
		);
	}

	/**
	 * Mediate HTTP requests to create new Data Set Templates and output
	 * the results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function createDataSetTemplate()
	{
		if (!$this->system->user->hasAdminPermissions('data_set_templates', 'create_data_set_template')) {
			return Redirect::route('admin.data-set-templates');
		}
		if ($this->input) {
			$data_set_template = \DataSetTemplatesHelper::extractDataSetTemplatesFromInput($this->input)->first();
			if ($this->data_set_templates_repo->write($data_set_template)) {
				$this->system->messages->add(
					array(
						'success' => array(
							'You successfully created the Data Set Template "' . $data_set_template->name() . '".',
						)
					)
				)->flash();
				return Redirect::route('admin.data-set-templates');
			}
			$this->system->messages->add($this->data_set_templates_repo->messages()->toArray());
		} else {
			$data_set_template = $this->data_set_templates_repo->newModel();
		}
		$messages = $this->system->messages->get();
		return View::make(
			'data::data_set_templates.create_data_set_template',
			compact('messages', 'data_set_template')
		);
	}

	/**
	 * Mediate HTTP requests to update an existing Data Set Template and
	 * output the results.
	 *
	 * @param	Integer
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function editDataSetTemplate($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_set_templates', 'edit_data_set_template')) {
			return Redirect::route('admin.data-set-templates');
		}
		if ($data_set_template = $this->data_set_templates_repo->retrieve($id)) {
			if ($this->input) {
				$data_set_template = \DataSetTemplatesHelper::extractDataSetTemplatesFromInput($this->input)->first();
				$data_set_template->setID($id);
				if ($this->data_set_templates_repo->write($data_set_template)) {
					$this->system->messages->add(
						array(
							'success' => array(
								'You successfully updated the Data Set Template "' . $data_set_template->name() . '".',
							)
						)
					)->flash();
					return Redirect::route('admin.data-set-templates');
				}
				$this->system->messages->add($this->data_set_templates_repo->messages()->toArray());
			}
			$messages = $this->system->messages->get();
			return View::make(
				'data::data_set_templates.edit_data_set_template',
				compact('messages', 'data_set_template')
			);
		}
		return Redirect::route('admin.data-set-templates');
	}
}