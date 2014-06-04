<div class="well">
	<div class="control_block">
		{{ Form::label('name', 'Name', array('class' => 'label label--block')) }}
		{{ Form::input('text', 'name', $name, array('class' => 'input__text')) }}
	</div>
	<div class="control_block">
		{{ Form::label('table_prefix', 'Table Prefex', array('class' => 'label label--block')) }}
		{{ Form::input('text', 'table_prefix', $table_prefix, array('class' => 'input__text')) }}
	</div>
	<div class="control_block">
		{{ Form::label('table_name', 'Table Name', array('class' => 'label label--block')) }}
		{{ Form::input('text', 'table_name', null, array('class' => 'input__text input__text--disabled', 'disabled' => 'disabled')) }}
	</div>
</div>

<div class="js--data_sets">
	@foreach ($data_set_templates as $data_set_template)
		{{ $data_set_template->view(array(
			'show_validation' => $show_validation,
			'choose_component' => true,
			'removable' => true
		)) }}
	@endforeach
</div>

<div class="well align--right">
	<span class="js--add_data_set button button--teal">Add data set</span>
</div>

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