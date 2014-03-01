@extends('../dashboard')
@section('master-head')
	<script src="{{ URL::to('packages/fruitful/data/js/datasets.js') }}"></script>
	<script src="{{ URL::to('packages/fruitful/data/js/components.js') }}"></script>
@stop
@section('body-header')
	<h1 class="color--teal">Create Data Stream Template</h1>
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
				{{ Form::input('text', 'name', Input::has('name') ? Input::get('name') : null, array('class' => 'input--text')) }}
			</div>
		</div>

		<div class="js--data_sets">
			@foreach ($data_stream_template->dataSetTemplates() as $data_set_template)
				{{ $data_set_template->view(true, true) }}
			@endforeach
		</div>

		<div class="well align--right">
			<span class="js--add_data_set button button--teal">Add data set</span>
		</div>

		<div class="form__controls form__controls--standard control_block">
			<div class="form__controls__left">
				<a href="{{ URL::route('admin.data-stream-templates') }}" class="button button--mustard">Cancel</a>
			</div>
			<div class="form__controls__right align--right">
				{{ Form::submit('Create', array('class' => 'button button--wasabi')) }}
			</div>
		</div>
	{{ Form::close() }}

	<script>
		(function(window, jQuery){
			'use strict';
			$(document).ready(function(){
				$('.js--add_data_set').on('click', function(){
					datasets.add(function(view){
						$('.js--data_sets').append(view);
					});
				});
			});
		})(window, jQuery)
	</script>

@stop