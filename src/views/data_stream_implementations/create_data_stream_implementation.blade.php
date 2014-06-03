@extends('../dashboard')
@section('body-header')
	<h1 class="dashboard__title">Create Data Stream</h1>
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
			<div class="control_block">
				{{ Form::label('name', 'Name', array('class' => 'label label--block')) }}
				{{ Form::input('text', 'name', $data_stream->name(), array('class' => 'input__text')) }}
			</div>
			<div class="control_block">
				<label class="label label--block">Data Sets to display</label>
				<label class="label label--block label--description">Choose the Data Sets you want to display when previewing this Data Stream..</label>
				@foreach ($data_stream->template()->dataSetTemplates() as $key => $data_set_template)
					<?php $slug = 'preview-' . Str::slug($data_set_template->name()); ?>
					<label for="{{ $slug }}" class="label checkbox">
						{{ Form::checkbox($slug, $key, isset($data_stream->previewColumns()[$key]), array('class' => 'checkbox__input', 'id' => $slug)) }}
						<span class="checkbox__label">{{ $data_set_template->name() }}</span>
					</label>
				@endforeach
			</div>
		</div>
		<div class="form__controls form__controls--standard control_block">
			<div class="form__controls__left">
				<a href="{{ URL::route('admin.data-streams') }}" class="button button--mustard">Cancel</a>
			</div>
			<div class="form__controls__right align--right">
				{{ Form::submit('Create', array('class' => 'button button--wasabi')) }}
			</div>
		</div>
	{{ Form::close() }}
@stop