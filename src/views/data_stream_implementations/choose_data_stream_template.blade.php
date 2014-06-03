@extends('../dashboard')
@section('body-header')
	<h2 class="dashboard__subtitle">Create Data Stream</h2>
	<h1 class="dashboard__title">Choose Data Stream Template</h1>
@stop
@section('body-content')

	@if ($messages)
		<div class="node__y--bottom">
			<div class="message_box message_box--tomato">
				<span class="message_box__title">Great Scott!</span>
				<ul>
					@foreach($messages->all() as $message)
						<li>{{ $message }}</li>
					@endforeach
				</ul>
			</div>
		</div>
	@endif

	{{ Form::open() }}
		<div class="well">
			@if (count($data_stream_templates) == 1)
				<div class="message_box message_box--mustard">
					<ul>
						<span class="message_box__title">Hey There!</span>
						<li>New data streams are created by implementing data stream templates. Before you can create a new data stream you first need to have <a href="{{ URL::route('admin.data-stream-templates.create') }}">created a data stream template</a>.</li>
					</ul>
				</div>
			@endif
			<div class="control_block">
				{{ Form::label('data_stream_template', 'Use Template', array('class' => 'label label--block')) }}
				{{ Form::select('data_stream_template', $data_stream_templates, Input::has('data_stream_template') ? Input::get('data_stream_template') : null, array('class' => 'select')) }}
			</div>
		</div>
		<div class="form__controls form__controls--standard control_block">
			<div class="form__controls__left">
				<a href="{{ URL::route('admin.data-streams') }}" class="button button--mustard">Cancel</a>
			</div>
			<div class="form__controls__right align--right">
				{{ Form::submit('Use template', array('class' => 'button button--wasabi')) }}
			</div>
		</div>
	{{ Form::close() }}

@stop