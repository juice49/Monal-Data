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
use Fruitful\Data\Repositories\DataStreamsRepository;
use Fruitful\Data\Repositories\DataStreamTemplatesRepository;

class DataStreamsController extends AdminController
{
	/**
	 * An instance the of the Data Streams Repository.
	 *
	 * @var		 Fruitful\Data\Repositories\DataStreamsRepository
	 */
	protected $data_streams_repo;

	/**
	 * An instance the of the Data Stream Templates Repository.
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
		DataStreamsRepository $data_streams_repo,
		DataStreamTemplatesRepository $data_stream_templates_repo
		) {
		parent::__construct($system_gateway);
		$this->data_streams_repo = $data_streams_repo;
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
		$data_streams = $this->data_streams_repo->retrieve();
		$messages = $this->system->messages->get();
		return View::make('data::data_stream_implementations.data_stream_implementations', compact('messages', 'data_streams'));
	}

	/**
	 * Mediate HTTP requests to create new Data Stream and output the
	 * results.
	 *
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function chooseDataStreamTemplate()
	{
		if (!$this->system->user->hasAdminPermissions('data_streams', 'create_data_stream')) {
			return Redirect::route('admin.data-streams');
		}
		if ($this->input) {
			$validation = Validator::make(
				$this->input,
				array(
					'data_stream_template' => 'required|not_in:0',
				),
				array(
					'data_stream_template.required' => 'You need to choose a Template to use for this Data Set.',
					'data_stream_template.not_in' => 'You need to choose a Template to use for this Data Set.',
				)
			);
			if ($validation->passes()) {
				return Redirect::route('admin.data-streams.create', $this->input['data_stream_template']);
			}
			$this->system->messages->add($validation->messages()->toArray());
		}
		$data_stream_templates = array(0 => 'Choose template...');
		foreach ($this->data_stream_templates_repo->retrieve() as $data_stream_template) {
			$data_stream_templates[$data_stream_template->ID()] = $data_stream_template->name();
		}
		$messages = $this->system->messages->get();
		return View::make('data::data_stream_implementations.choose_data_stream_template', compact('messages', 'data_stream_templates'));
	}

	/**
	 * Mediate HTTP requests to create new Data Stream and output the
	 * results.
	 *
	 * @param	Integer
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function createDataStream($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_streams', 'create_data_stream')) {
			return Redirect::route('admin.data-streams');
		}
		$data_stream = $this->data_streams_repo->newModel();
		if ($data_stream_template = $this->data_stream_templates_repo->retrieve($id)) {
			$data_stream->setTemplate($data_stream_template);
			if ($this->input) {
				$data_stream->setName(isset($this->input['name']) ? $this->input['name'] : null);
				foreach ($this->input as $key => $value) {
					if (substr($key, 0, 8) === 'preview-') {
						$data_stream->addPreviewColumn($value);
					}
				}
				if ($this->data_streams_repo->write($data_stream)) {
					$this->system->messages->add(
						array(
							'success' => array(
								'You successfully created the Data Stream "' . $data_stream->name() . '".',
							)
						)
					)->flash();
					return Redirect::route('admin.data-streams');
				}
				$this->system->messages->add($this->data_streams_repo->messages()->toArray());
			}
			$messages = $this->system->messages->get();
			return View::make(
				'data::data_stream_implementations.create_data_stream_implementation',
				compact('messages', 'data_stream')
			);
		}
		return Redirect::route('admin.data-streams');
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
	public function createDataStreamTemplate()
	{
		if (!$this->system->user->hasAdminPermissions('data_stream_templates', 'create_data_stream_template')) {
			return Redirect::route('admin.data-stream-templates');
		}
		$data_stream_template = $this->data_stream_templates_repo->newModel();
		$data_set_templates = \DataSetTemplatesHelper::extractDataSetTemplatesFromInput($this->input);
		if ($this->input) {
			$data_stream_template->setName(isset($this->input['name']) ? $this->input['name'] : null);
			$data_stream_template->setTablePrefix(isset($this->input['table_prefix']) ? $this->input['table_prefix'] : null);
			foreach ($data_set_templates as $data_set_template) {
				$data_stream_template->addDataSetTemplate($data_set_template);
			}
			if ($this->data_stream_templates_repo->validatesForStorage($data_stream_template)) {
				if (\StreamSchema::build($data_stream_template)) {
					$this->data_stream_templates_repo->write($data_stream_template);
					$this->system->messages->add(
						array(
							'success' => array(
								'You successfully created the Data Stream Template "' . $data_stream_template->name() . '".',
							)
						)
					)->flash();
					return Redirect::route('admin.data-stream-templates');
				}
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
	public function editDataStreamTemplate($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_stream_templates', 'edit_data_stream_template')) {
			return Redirect::route('admin.data-stream-templates');
		}
		if ($data_stream_template = $this->data_stream_templates_repo->retrieve($id)) {
			if ($this->input) {
				$data_stream_template->setName($this->input['name']);
				$data_stream_template->setTablePrefix(isset($this->input['table_prefix']) ? $this->input['table_prefix'] : null);
				$data_stream_template->discardDataSetTemplates();
				$data_set_templates = \DataSetTemplatesHelper::extractDataSetTemplatesFromInput($this->input);
				foreach ($data_set_templates as $data_set_template) {
					$data_stream_template->addDataSetTemplate($data_set_template);
				}
				if ($this->data_stream_templates_repo->validatesForStorage($data_stream_template)) {
					if (\StreamSchema::update($data_stream_template)) {
						$this->data_stream_templates_repo->write($data_stream_template);
						$this->system->messages->add(
							array(
								'success' => array(
									'You successfully updated the Data Stream Template "' . $data_stream_template->name() . '".',
								)
							)
						)->flash();
						return Redirect::route('admin.data-stream-templates');
					}
					$this->system->messages->add(
						array(
							'error' => array(
								'There was an error making these updates.',
							)
						)
					);
				} else {
					$this->system->messages->add($this->data_stream_templates_repo->messages()->toArray());
				}
			}
			$messages = $this->system->messages->get();
			return View::make(
				'data::data_stream_templates.edit_data_stream_template',
				compact('messages', 'data_stream_template')
			);
		}
		return Redirect::route('admin.data-stream-templates');
	}
}