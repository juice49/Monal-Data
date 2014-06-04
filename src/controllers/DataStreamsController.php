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

use Monal\GatewayInterface;

class DataStreamsController extends AdminController
{
	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct(GatewayInterface $system_gateway) {
		parent::__construct($system_gateway);
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
		$data_streams = DataStreamsRepository::retrieve();
		$messages = $this->system->messages->get();
		return View::make('data::data_streams.data_streams', compact('messages', 'data_streams'));
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
		foreach (DataStreamTemplatesRepository::retrieve() as $data_stream_template) {
			$data_stream_templates[$data_stream_template->ID()] = $data_stream_template->name();
		}
		$messages = $this->system->messages->get();
		return View::make('data::data_streams.choose', compact('messages', 'data_stream_templates'));
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
		$data_stream = DataStreamsRepository::newModel();
		if ($data_stream_template = DataStreamTemplatesRepository::retrieve($id)) {
			$data_stream->setTemplate($data_stream_template);
			if ($this->input) {
				$data_stream->setName(isset($this->input['name']) ? $this->input['name'] : null);
				foreach ($this->input as $key => $value) {
					if (substr($key, 0, 8) === 'preview-') {
						$data_stream->addPreviewColumn($value);
					}
				}
				if (DataStreamsRepository::write($data_stream)) {
					$this->system->messages->add(
						array(
							'success' => array(
								'You successfully created the Data Stream "' . $data_stream->name() . '".',
							)
						)
					)->flash();
					return Redirect::route('admin.data-streams');
				}
			}
			return View::make('data::data_streams.create', compact('data_stream'));
		}
		return Redirect::route('admin.data-streams');
	}

	/**
	 * Mediate HTTP requests to update an existing Data Stream and output
	 * the results.
	 *
	 * @param	Integer
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function editDataStream($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_streams', 'edit_data_stream')) {
			return Redirect::route('admin.data-streams');
		}
		if ($data_stream = DataStreamsRepository::retrieve($id)) {
			if ($this->input) {
				$data_stream->setName(isset($this->input['name']) ? $this->input['name'] : null);
				$data_stream->discardPreviewColumns();
				foreach ($this->input as $key => $value) {
					if (substr($key, 0, 8) === 'preview-') {
						$data_stream->addPreviewColumn($value);
					}
				}
				if (DataStreamsRepository::write($data_stream)) {
					$this->system->messages->add(
						array(
							'success' => array(
								'You successfully updated the Data Stream "' . $data_stream->name() . '".',
							)
						)
					)->flash();
					return Redirect::route('admin.data-streams');
				}
			}
			return View::make('data::data_streams.edit', compact('data_stream'));
		}
		return Redirect::route('admin.data-streams');
	}

	/**
	 * Mediate HTTP requests to view an existing Data Stream and output
	 * the results.
	 *
	 * @param	Integer
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function viewDataStream($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_streams')) {
			return Redirect::route('admin');
		}
		if ($data_stream = DataStreamsRepository::retrieve($id)) {
			$messages = $this->system->messages->get();
			return View::make(
				'data::data_streams.view_entries',
				compact('messages', 'data_stream')
			);
		}
		return Redirect::route('admin.data-streams');
	}

	/**
	 * Mediate HTTP requests to add a new entry to a Data Stream and
	 * output the results.
	 *
	 * @param	Integer
	 * @return	Illuminate\View\View / Illuminate\Http\RedirectResponse
	 */
	public function addEntry($id)
	{
		if (!$this->system->user->hasAdminPermissions('data_streams', 'create_stream_entry')) {
			return Redirect::route('admin.data-streams');
		}
		if ($data_stream = DataStreamsRepository::retrieve($id)) {
			$stream_entry = $data_stream->newEntryModel();
			if ($this->input) {
				foreach (\DataSetsHelper::extractDataSetValuesFromInput($this->input) as $key => $data_set_values) {
					$stream_entry->dataSets()[$key]->setComponentValues($data_set_values['component_values']);
				}
				if ($data_stream->addEntry($stream_entry)) {
					$this->system->messages->add(
						array(
							'success' => array(
								'You successfully added a new entry to the Data Stream "' . $data_stream->name() . '"',
							)
						)
					)->flash();
					return Redirect::route('admin.data-streams.view', $data_stream->ID());
				}
				$this->system->messages->add($data_stream->messages()->toArray());
			}
			$messages = $this->system->messages->get();
			return View::make(
				'data::data_streams.add_entry',
				compact('messages', 'data_stream', 'stream_entry')
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
		$data_stream_templates = DataStreamTemplatesRepository::retrieve();
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
		$data_stream_template = DataStreamTemplatesRepository::newModel();
		$data_stream_template->setTablePrefix('stream__');
		if ($this->input) {
			$data_stream_template->setName(isset($this->input['name']) ? $this->input['name'] : null);
			$data_stream_template->setTablePrefix(isset($this->input['table_prefix']) ? $this->input['table_prefix'] : null);
			$data_set_templates = \DataSetTemplatesHelper::extractDataSetTemplatesFromInput($this->input);
			foreach ($data_set_templates as $data_set_template) {
				$data_stream_template->addDataSetTemplate($data_set_template);
			}
			if (DataStreamTemplatesRepository::validatesForStorage($data_stream_template)) {
				if (\StreamSchema::build($data_stream_template)) {
					DataStreamTemplatesRepository::write($data_stream_template);
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
			$this->system->messages->add(DataStreamTemplatesRepository::messages()->toArray());
		}
		$messages = $this->system->messages->get();
		$this->system->dashboard->addScript('packages/monal/data/js/datasets.js');
		$this->system->dashboard->addScript('packages/monal/data/js/components.js');
		return View::make('data::data_stream_templates.create', compact('messages', 'data_stream_template'));
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
		if ($data_stream_template = DataStreamTemplatesRepository::retrieve($id)) {
			if ($this->input) {
				$from = clone $data_stream_template;
				$data_stream_template->setName($this->input['name']);
				$data_stream_template->setTablePrefix(isset($this->input['table_prefix']) ? $this->input['table_prefix'] : null);
				$data_stream_template->discardDataSetTemplates();
				$data_set_templates = \DataSetTemplatesHelper::extractDataSetTemplatesFromInput($this->input);
				foreach ($data_set_templates as $data_set_template) {
					$data_stream_template->addDataSetTemplate($data_set_template);
				}
				if (DataStreamTemplatesRepository::validatesForStorage($data_stream_template)) {
					if (\StreamSchema::update($from, $data_stream_template)) {
						DataStreamTemplatesRepository::write($data_stream_template);
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
					$this->system->messages->add(DataStreamTemplatesRepository::messages()->toArray());
				}
			}
			$messages = $this->system->messages->get();
			$this->system->dashboard->addScript('packages/monal/data/js/datasets.js');
			$this->system->dashboard->addScript('packages/monal/data/js/components.js');
			return View::make(
				'data::data_stream_templates.edit',
				compact('messages', 'data_stream_template')
			);
		}
		return Redirect::route('admin.data-stream-templates');
	}
}