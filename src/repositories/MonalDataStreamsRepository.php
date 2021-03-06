<?php
namespace Monal\Data\Repositories;
/**
 * Monal Data Streams Repository.
 *
 * The Monal System's implementation of the DataStreamsRepository.
 *
 * @author	Arran Jacques
 */

use Monal\Data\Repositories\DataStreamsRepository;
use Monal\Data\Models\DataStream;

class MonalDataStreamsRepository implements DataStreamsRepository
{
	/**
	 * The repository's messages.
	 *
	 * @var		 Monal\Core\Contracts\MessagesInterface
	 */
	protected $messages;

	/**
	 * The database table the repository uses.
	 *
	 * @var		String
	 */
	protected $table = 'data_streams';

	/**
	 * Constructor.
	 *
	 * @return	Void
	 */
	public function __construct()
	{
		$this->messages = \App::make('Monal\Core\Contracts\MessagesInterface');
	}

	/**
	 * Return the repository's messages.
	 *
	 * @return	Illuminate\Support\MessageBag
	 */
	public function messages()
	{
		return $this->messages->get();
	}

	/**
	 * Return a new Data Stream model.
	 *
	 * @return	Monal\Data\Models\DataStream
	 */
	public function newModel()
	{
		return \App::make('Monal\Data\Models\DataStream');
	}

	/**
	 * Check a Data Stream model validates for storage.
	 *
	 * @param	Monal\Data\Models\DataStream
	 * @return	Boolean
	 */
	public function validatesForStorage(DataStream $data_stream)
	{
		// Allow alpha, numeric, hypens, underscores and space characters.
		\Validator::extend('data_stream_name', function($attribute, $value, $parameters)
		{
			return preg_match('/^[a-z0-9 \-_]+$/i', $value) ? true : false;
		});
		$unique_exception = ($data_stream->ID()) ? ',' . $data_stream->ID() : null;
		$validation_rules = array(
			'name' => 'required|max:100|data_stream_name|unique:data_streams,name' . $unique_exception,
		);
		$validation_messages = array(
			'name.required' => 'You need to give this Data Stream a Name.',
			'name.max' => 'The Name of this Data Stream must be no more than 100 characters long.',
			'name.data_stream_name' => 'The Name for this Data Stream can only contain letters, numbers, spaces, underscores and hyphens.',
			'name.unique' => 'Aw shucks! This Name has already been used.',
		);
		if ($data_stream->validates($validation_rules, $validation_messages)) {
			$stream_validates = true;
		} else {
			$stream_validates = false;
			$this->messages->add($data_stream->messages()->toArray());
		}
		$template_validates = ($data_stream->template() AND $data_stream->template()->ID()) ? true : false;
		return ($stream_validates AND $template_validates) ? true : false;
	}

	/**
	 * Encode a Data Stream model so it is ready to be stored in the
	 * repository.
	 *
	 * @param	Monal\Data\Models\DataStream
	 * @return	Array
	 */
	protected function encodeForStorage(DataStream $data_stream)
	{
		$encoded = array(
			'name' => $data_stream->name(),
			'template' => $data_stream->template()->ID(),
			'preview_columns' => '',
		);
		foreach ($data_stream->previewColumns() as $preview_column) {
			$encoded['preview_columns'] .= $preview_column . ',';
		}
		$encoded['preview_columns'] = rtrim($encoded['preview_columns'], ',');
		return $encoded;
	}

	/**
	 * Decode a Data Stream repository entry into its model class.
	 *
	 * @param	stdClass
	 * @return	Monal\Data\Models\DataStream
	 */
	public function decodeFromStorage($result)
	{
		$data_stream = $this->newModel();
		$data_stream->setID($result->id);
		$data_stream->setName($result->name);
		if ($result->preview_columns != '') {
			foreach (explode(',', $result->preview_columns) as $preview_column) {
				$data_stream->addPreviewColumn($preview_column);
			}
		}
		$data_stream_templates_repo = \App::make('Monal\Data\Repositories\DataStreamTemplatesRepository');
		$template = new \stdClass;
		$template->id = $result->template_id;
		$template->name = $result->template_name;
		$template->table_prefix = $result->template_table_prefix;
		$template->data_set_templates = $result->template_data_set_templates;
		$data_stream->setTemplate($data_stream_templates_repo->decodeFromStorage($template));
		return $data_stream;
	}

	/**
	 * Retrieve an instance/s from the repository.
	 *
	 * @param	Integer
	 * @return	Illuminate\Database\Eloquent\Collection / Monal\Data\Models\DataStream
	 */
	public function retrieve($key = null)
	{
		$query = \DB::table($this->table)->select(
			array(
				'data_streams.id',
				'data_streams.name',
				'data_streams.template',
				'data_streams.preview_columns',
				'data_stream_templates.id as template_id',
				'data_stream_templates.name as template_name',
				'data_stream_templates.table_prefix as template_table_prefix',
				'data_stream_templates.data_set_templates as template_data_set_templates',
			)
		) ->join('data_stream_templates', 'data_streams.template', '=', 'data_stream_templates.id');
		if (!$key) {
			$results = $query->get();
			$data_streams = \App::make('Illuminate\Database\Eloquent\Collection');
			foreach ($results as $result) {
				$data_streams->add($this->decodeFromStorage($result));
			}
			return $data_streams;
		} else {
			if ($result = $query->where('data_streams.id', '=', $key)->first()) {
				return $this->decodeFromStorage($result);
			}
		}
		return false;
	}

	/**
	 * Write a Data Stream model to the repository.
	 *
	 * @param	Monal\Data\Models\DataStream
	 * @return	Boolean
	 */
	public function write(DataStream $data_stream)
	{
		if ($this->validatesForStorage($data_stream)) {
			$encoded = $this->encodeForStorage($data_stream);
			if ($data_stream->ID()) {
				$encoded['updated_at'] = date('Y-m-d H:i:s');
				\DB::table($this->table)->where('id', '=', $data_stream->ID())->update($encoded);
				return true;
			} else {
				$encoded['created_at'] = date('Y-m-d H:i:s');
				$encoded['updated_at'] = date('Y-m-d H:i:s');
				\DB::table($this->table)->insert($encoded);
				return true;
			}
		}
		return false;
	}
}