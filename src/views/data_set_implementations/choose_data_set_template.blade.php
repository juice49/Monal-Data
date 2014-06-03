@extends('../dashboard')
@section('body-header')
	<h2 class="dashboard__subtitle">Create Data Set</h2>
	<h1 class="dashboard__title">Choose Data Set Template</h1>
@stop
@section('body-content')

	@if ($messages)
		<div class="message_box message_box--tomato">
			<span class="message_box__title">Great Scott!</span>
			<ul>
				@foreach($messages->all() as $message)
					<li>{{ $message }}</li>
				@endforeach
			</ul>
		</div> 
	@endif

	{{ Form::open() }}
		<div class="well">
			@if (count($data_set_templates) == 1)
				<div class="message_box message_box--mustard">
					<span class="message_box__title">Hey There!</span>
					<ul>
						<li>New data sets are created by implementing data set templates. Before you can create a new data set you first need to have <a href="{{ URL::route('admin.data-set-templates.create') }}">created a data set template</a>.</li>
					</ul>
				</div>
			@endif
			<div class="control_block">
				{{ Form::label('data_set_template', 'Use Template', array('class' => 'label label--block')) }}
				{{ Form::select('data_set_template', $data_set_templates, Input::has('data_set_template') ? Input::get('data_set_template') : null, array('class' => 'select')) }}
			</div>
		</div>
		<div class="form__controls form__controls--standard control_block">
			<div class="form__controls__left">
				<a href="{{ URL::route('admin.data-sets') }}" class="button button--mustard">Cancel</a>
			</div>
			<div class="form__controls__right align--right">
				{{ Form::submit('Use template', array('class' => 'button button--wasabi')) }}
			</div>
		</div>
	{{ Form::close() }}

@stop