<div class="control_block">
	<div class="control_block">
		{{ Form::label($uri . '-type', 'Type', array('class' => 'label--block')) }}
		<div class="select__default">
			{{ Form::select($uri . '-type', $types, isset($settings['type']) ? $settings['type'] : null, array('class' => 'js--type--' . $uri . ' select')) }}
		</div>
	</div>
	<div class="control_block">
		{{ Form::checkbox($uri . '-limit', 1, isset($settings['limit']) ? $settings['limit'] : null, array('class' => 'js--limit--' . $uri . ' input--checkbox data_set', 'id' => $uri . '-limit')) }}
		{{ Form::label($uri . '-limit', null, array('class' => 'input--checkbox__default input--checkbox__inline')) }}
		{{ Form::label($uri . '-limit', 'Limit Length', array('class' => 'label--inline')) }}
	</div>
	<div class="js--limit_settings--{{ $uri }}">
		<div class="control_block">
			{{ Form::label($uri . '-limit_length', 'Limit To', array('class' => 'label--block')) }}
			{{ Form::input('text', $uri . '-limit_length', isset($settings['limit_length']) ? $settings['limit_length'] : null, array('class' => 'input--text', 'placeholder' => 'e.g., 100')) }}
		</div>
		<div class="control_block">
			<div class="select__default">
				{{ Form::select($uri . '-limit_type', $limit_types, isset($settings['limit_type']) ? $settings['limit_type'] : null, array('class' => 'select')) }}
			</div>
		</div>
	</div>
</div>
<script>
(function(window, $){

	'use strict';

	$(document).ready(function() {

		if (!$('.js--limit--{{ $uri }}').is(':checked')) {
			$('.js--limit_settings--{{ $uri }}').hide();
		}

		$('.js--limit--{{ $uri }}').on('change', function() {
			if ($(this).is(':checked')) {
				$('.js--limit_settings--{{ $uri }}').slideDown(200);
			}
			else {
				$('.js--limit_settings--{{ $uri }}').slideUp(200);
			}
		});
	});
})(window, jQuery);
</script>