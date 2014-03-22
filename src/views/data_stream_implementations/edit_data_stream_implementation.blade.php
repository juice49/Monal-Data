@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">Edit Data Stream</h1>
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
				{{ Form::label('name', 'Name', array('class' => 'label--block')) }}
				{{ Form::input('text', 'name', $data_stream->name(), array('class' => 'input--text')) }}
			</div>
			<div class="control_block">
				<label class="label--block">Data Sets to display</label>
				<label class="label--block label--description">Choose the Data Sets you want to display when previewing this Data Stream..</label>
			</div>
			@foreach ($data_stream->template()->dataSetTemplates() as $key => $data_set_template)
				<?php $slug = 'preview-' . Str::slug($data_set_template->name()); ?>
				<div class="control_block">
					{{ Form::checkbox($slug, $key, isset($data_stream->previewColumns()[$key]), array('class' => 'input--checkbox', 'id' => $slug)) }}
					{{ Form::label($slug, null, array('class' => 'input--checkbox__default input--checkbox__inline')) }}
					{{ Form::label($slug, $data_set_template->name(), array('class' => 'label--inline')) }}
				</div>
			@endforeach
		</div>
		<div class="form__controls form__controls--standard control_block">
			<div class="form__controls__left">
				<a href="{{ URL::route('admin.data-streams') }}" class="button button--mustard">Cancel</a>
			</div>
			<div class="form__controls__right align--right">
				{{ Form::submit('Update', array('class' => 'button button--wasabi')) }}
			</div>
		</div>
	{{ Form::close() }}
@stop