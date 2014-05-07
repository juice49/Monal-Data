@extends('../dashboard')
@section('master-head')
	<script src="{{ URL::to('packages/monal/data/js/datasets.js') }}"></script>
	<script src="{{ URL::to('packages/monal/data/js/components.js') }}"></script>
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
				{{ Form::input('text', 'name', $data_stream_template->name(), array('class' => 'input--text')) }}
			</div>
			<div class="control_block">
				{{ Form::label('table_prefix', 'Table Prefex', array('class' => 'label--block')) }}
				{{ Form::input('text', 'table_prefix', $data_stream_template->tablePrefix(), array('class' => 'input--text')) }}
			</div>
			<div class="control_block">
				{{ Form::label('table_name', 'Table Name', array('class' => 'label--block')) }}
				{{ Form::input('text', 'table_name', null, array('class' => 'input--text input--disabled', 'disabled' => 'disabled')) }}
			</div>
		</div>

		<div class="js--data_sets">
			@foreach ($data_stream_template->dataSetTemplates() as $data_set_template)
				{{ $data_set_template->view(true, true, true) }}
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

			function tableName() {
				$('#table_name').val(snakeCaseString($('#table_prefix').val() + $('#name').val()));
			}

			$(document).ready(function(){
				$('.js--add_data_set').on('click', function(){
					datasets.add(function(view){
						$('.js--data_sets').append(view);
					});
				});
				$('#table_prefix, #name').on('keyup', tableName).on('change', tableName);
				tableName();
			});
		})(window, jQuery);
	</script>

@stop