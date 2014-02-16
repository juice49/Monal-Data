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

use Fruitful\Core\Contracts\GatewayInterface;
use Fruitful\Data\Contracts\DataSetTemplatesInterface;
use Fruitful\Data\Contracts\ComponentsInterface;
use Fruitful\Data\Repositories\Contracts\DataSetTemplatesRepository;

class DataSetsController extends AdminController
{
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
	 * Instance of class implementing DataSetTemplatesRepository.
	 *
	 * @var		 Fruitful\Data\Contracts\ComponentsInterface
	 */
	protected $repository;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct(
		GatewayInterface $system_gateway,
		DataSetTemplatesInterface $data_set_templates,
		ComponentsInterface $components,
		DataSetTemplatesRepository $repository
		) {
		parent::__construct($system_gateway);
		$this->data_set_templates = $data_set_templates;
		$this->components = $components;
		$this->repository = $repository;
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
		$messages = $this->system->messages->get();
		return View::make('data::data_set_instances.data_sets', compact('messages'));
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
		$data_set_templates = $this->repository->retrieve();
		$messages = $this->system->messages->get();
		return View::make('data::data_set_templates.data_set_templates', compact('messages', 'data_set_templates'));
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
			$data_set_template = $this->data_set_templates->extractDataSetTemplatesFromInput($this->input)->first();
			if ($this->repository->write($data_set_template)) {
				$this->system->messages->add(
					array(
						'success' => array(
							'You successfully created the Data Set Template "' . $data_set_template->name . '".',
						)
					)
				)->flash();
				return \Redirect::route('admin.data-set-templates');
			}
			$this->system->messages->add($this->repository->messages()->toArray());
		} else {
			$data_set_template = $this->data_set_templates->make();
		}
		$instance_view = $data_set_template->view(true);
		$messages = $this->system->messages->get();
		return View::make('data::data_set_templates.create_data_set_template', compact('messages', 'instance_view'));
	}

	/**
	 * Mediate HTTP requests to update an existing Data Set Template and
	 * output the results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function editDataSetTemplate($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_set_templates', 'edit_data_set_template')) {
			return Redirect::route('admin.data-set-templates');
		}
		if ($data_set_template = $this->repository->retrieve($id)) {
			if ($this->input) {
				$data_set_template = $this->data_set_templates->extractDataSetTemplatesFromInput($this->input)->first();
				$data_set_template->setID($id);
				if ($this->repository->write($data_set_template)) {
					$this->system->messages->add(
						array(
							'success' => array(
								'You successfully updated the Data Set Template "' . $data_set_template->name . '".',
							)
						)
					)->flash();
					return \Redirect::route('admin.data-set-templates');
				}
				$this->system->messages->add($this->repository->messages()->toArray());
			}
			$instance_view = $data_set_template->view(true);
			$messages = $this->system->messages->get();
			return View::make('data::data_set_templates.edit_data_set_template', compact('messages', 'instance_view'));
		}
		return Redirect::route('admin.data-set-templates');
	}
}