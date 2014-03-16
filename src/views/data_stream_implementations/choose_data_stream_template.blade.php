@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">Choose Data Stream Template</h1>
@stop
@section('body-content')

	@if ($messages)
		<div class="message_box message_box--tomato">
			<h6>Great Scott!</h6>
			<ul>
				@foreach($messages->all() as $message)
					<li>{{ $message }}</li>
				@endforeach
			</ul>
		</div> 
	@endif

	{{ Form::open() }}
		<div class="well">
			@if (count($data_stream_templates) == 1)
				<div class="message_box message_box--mustard">
					<ul>
						<h6>HEY THERE!</h6>
						<li>Before you can create a Data Stream you first need to create a Template for the Stream to implement.</li>
					</ul>
				</div>
			@endif
			<div class="control_block">
				{{ Form::label('data_stream_template', 'Use Template', array('class' => 'label--block')) }}
				<div class="select__default">
					{{ Form::select('data_stream_template', $data_stream_templates, Input::has('data_stream_template') ? Input::get('data_stream_template') : null, array('class' => 'select')) }}
				</div>
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