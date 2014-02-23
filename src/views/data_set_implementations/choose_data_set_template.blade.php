@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">Create Data Set</h1>
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
			<div class="control_block">
				{{ Form::label('data_set_template', 'Use Template', array('class' => 'label--block')) }}
				<div class="select__default">
					{{ Form::select('data_set_template', $data_set_templates, Input::has('data_set_template') ? Input::get('data_set_template') : null, array('class' => 'select')) }}
				</div>
			</div>
		</div>
		<div class="form__controls form__controls--standard control_block">
			<div class="form__controls__left">
				<a href="{{ URL::route('admin.data-sets') }}" class="button button--mustard">Cancel</a>
			</div>
			<div class="form__controls__right align--right">
				{{ Form::submit('Create', array('class' => 'button button--wasabi')) }}
			</div>
		</div>
	{{ Form::close() }}

@stop