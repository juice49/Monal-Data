<?php
namespace Fruitful\Data\Repositories;
/**
 * Eloquent Data Streams Repository.
 *
 * The Fruitful System's implementation of the DataStreamsRepository
 * using Laravel’s Eloquent ORM.
 *
 * @author	Arran Jacques
 */

use Fruitful\Data\Repositories\DataStreamsRepository;
use Fruitful\Data\Models\DataStream;

class EloquentDataStreamsRepository extends \Eloquent implements DataStreamsRepository
{
	/**
	 * The repository's messages.
	 *
	 * @var		 Fruitful\Core\Contracts\MessagesInterface
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
		$this->messages = \App::make('Fruitful\Core\Contracts\MessagesInterface');
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
	 * @return	Fruitful\Data\Models\DataStream
	 */
	public function newModel()
	{
		return \App::make('Fruitful\Data\Models\DataStream');
	}

	/**
	 * Check a Data Stream model validates for storage.
	 *
	 * @param	Fruitful\Data\Models\DataStream
	 * @return	Boolean
	 */
	public function validatesForStorage(DataStream $data_stream)
	{
		$unique_exception = ($data_stream->ID()) ? ',' . $data_stream->ID() : null;
		$validation_rules = array(
			'name' => 'required|username|unique:data_streams,name' . $unique_exception,
		);
		$validation_messages = array(
			'name.required' => 'You need to give this Data Stream a Name.',
			'name.username' => 'The Name for this Data Stream can only contain letters, numbers, spaces, underscores and hyphens.',
			'name.unique' => 'Sorry, it looks like someone beat you to the punch as that Name has already taken.',
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
	 * @param	Fruitful\Data\Models\DataStream
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
	 * @return	Fruitful\Data\Models\DataStream
	 */
	public function decodeFromStorage($result)
	{
		$data_stream = $this->newModel();
		$data_stream->setID($result->id);
		$data_stream->setName($result->name);
		foreach (explode(',', $result->preview_columns) as $preview_column) {
			$data_stream->addPreviewColumn($preview_column);
		}
		$data_stream_templates_repo = \App::make('Fruitful\Data\Repositories\DataStreamTemplatesRepository');
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
	 * @return	Illuminate\Database\Eloquent\Collection / Fruitful\Data\Models\DataStream
	 */
	public function retrieve($key = null)
	{
		if (!$key) {
			$results = \DB::table('data_streams')->select(
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
			)
			->join('data_stream_templates', 'data_streams.id', '=', 'data_stream_templates.id')
			->get();
			$data_streams = \App::make('Illuminate\Database\Eloquent\Collection');
			foreach ($results as &$result) {
				$data_streams->add($this->decodeFromStorage($result));
			}
			return $data_streams;
		} else {
			if ($result = self::find($key)) {
				return $result->decodeFromStorage();
			}
		}
		return false;
	}

	/**
	 * Write a Data Stream model to the repository.
	 *
	 * @param	Fruitful\Data\Models\DataStream
	 * @return	Boolean
	 */
	public function write(DataStream $data_stream)
	{
		if ($this->validatesForStorage($data_stream)) {
			$encoded = $this->encodeForStorage($data_stream);
			if ($data_stream->ID()) {
				if (
					$this->where('id', '=', $data_stream_template->ID())->update(
							array(
							'name' => $encoded['name'],
							'data_set_templates' => $encoded['data_set_templates'],
						)
					)
				) {
					return true;
				}
			} else {
				$entry = new self;
				$entry->name = $encoded['name'];
				$entry->template = $encoded['template'];
				$entry->preview_columns = $encoded['preview_columns'];
				return $entry->save() ? true : false;
			}
		}
		return false;
	}
}