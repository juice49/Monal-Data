<?php
/**
 * Data Streams Controller.
 *
 * Controller for Data Streams Admin pages. Acts as a mediator
 * between incoming HTTP requests and the application. Receives the
 * HTTP requests and runs the appropriate application layer logic,
 * outputting the results to a user interface.
 *
 * @author	Arran Jacques
 */

use Fruitful\Core\Contracts\GatewayInterface;
use Fruitful\Data\Libraries\DataSetTemplatesInterface;
use Fruitful\Data\Repositories\DataStreamTemplatesRepository;

class DataStreamsController extends AdminController
{
	/**
	 * An instance of the Data Sets library.
	 *
	 * @var		  Fruitful\Data\Libraries\DataSetTemplatesInterface
	 */
	protected $data_set_templates;

	/**
	 * An instance the of the Data Streams Template Repository.
	 *
	 * @var		 Fruitful\Data\Repositories\DataStreamTemplatesRepository
	 */
	protected $data_stream_templates_repo;

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct(
		GatewayInterface $system_gateway,
		DataSetTemplatesInterface $data_set_templates,
		DataStreamTemplatesRepository $data_stream_templates_repo
		) {
		parent::__construct($system_gateway);
		$this->data_set_templates = $data_set_templates;
		$this->data_stream_templates_repo = $data_stream_templates_repo;
	}

	/**
	 * Mediate HTTP requests to retrieve Data Streams and output the
	 * results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function dataStreams()
	{
		if (!$this->system->user->hasAdminPermissions('data_streams')) {
			return Redirect::route('admin.dashboard');
		}
		$messages = $this->system->messages->get();
		return View::make('data::data_stream_implementations.data_stream_implementations', compact('messages'));
	}

	/**
	 * Mediate HTTP requests to retrieve Data Stream Templates and output
	 * the results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function dataStreamTemplates()
	{
		if (!$this->system->user->hasAdminPermissions('data_stream_templates')) {
			return Redirect::route('admin.dashboard');
		}
		$data_stream_templates = $this->data_stream_templates_repo->retrieve();
		$messages = $this->system->messages->get();
		return View::make('data::data_stream_templates.data_stream_templates', compact('messages', 'data_stream_templates'));
	}

	/**
	 * Mediate HTTP requests to create new Data Stream Templates and
	 * output the results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function createStreamTemplate()
	{
		if (!$this->system->user->hasAdminPermissions('data_stream_templates', 'create_data_stream_template')) {
			return Redirect::route('admin.dashboard');
		}
		$data_stream_template = $this->data_stream_templates_repo->newModel();
		$data_set_templates = $this->data_set_templates->extractDataSetTemplatesFromInput($this->input);
		if ($this->input) {
			$data_stream_template->setName($this->input['name']);
			foreach ($data_set_templates as $data_set_template) {
				$data_stream_template->addDataSetTemplate($data_set_template);
			}
			if ($this->data_stream_templates_repo->write($data_stream_template)) {
				$this->system->messages->add(
					array(
						'success' => array(
							'You successfully created the Data Stream Template "' . $data_stream_template->name() . '".',
						)
					)
				)->flash();
				return Redirect::route('admin.data-stream-templates');
			}
			$this->system->messages->add($this->data_stream_templates_repo->messages()->toArray());
		}
		$messages = $this->system->messages->get();
		return View::make('data::data_stream_templates.create_data_stream_template', compact('messages', 'data_stream_template'));
	}

	/**
	 * Mediate HTTP requests to update an existing Data Stream Template
	 * and output the results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function editStreamTemplate($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_stream_templates', 'edit_data_stream_template')) {
			return Redirect::route('admin.dashboard');
		}
		if ($data_stream_template = $this->data_stream_templates_repo->retrieve($id)) {
			if ($this->input) {
				$data_stream_template->setName($this->input['name']);
				$data_stream_template->discardDataSetTemplates();
				$data_set_templates = $this->data_set_templates->extractDataSetTemplatesFromInput($this->input);
				foreach ($data_set_templates as $data_set_template) {
					$data_stream_template->addDataSetTemplate($data_set_template);
				}
				if ($this->data_stream_templates_repo->write($data_stream_template)) {
					$this->system->messages->add(
						array(
							'success' => array(
								'You successfully updated the Data Stream Template "' . $data_stream_template->name() . '".',
							)
						)
					)->flash();
					return Redirect::route('admin.data-stream-templates');
				}
				$this->system->messages->add($this->data_stream_templates_repo->messages()->toArray());
			}
			$messages = $this->system->messages->get();
			return View::make('data::data_stream_templates.edit_data_stream_template', compact('messages', 'data_stream_template'));
		}
		return 'no';
	}
}