<div class="control_block">
	<div class="control_block">
		{{ Form::label($uri . '-type', 'Type', array('class' => 'label label--block')) }}
		{{ Form::select($uri . '-type', $types, isset($settings['type']) ? $settings['type'] : null, array('class' => 'js--type--' . $uri . ' select')) }}
	</div>
	<div class="control_block">
		<label for="{{ $uri . '-limit' }}" class="label checkbox">
			{{ Form::checkbox($uri . '-limit', 1, isset($settings['limit']) ? $settings['limit'] : null, array('class' => 'js--limit--' . $uri . ' checkbox__input data_set', 'id' => $uri . '-limit')) }}
			<span class="checkbox__label">Limit Length</span>
		</label>
	</div>
	<div class="js--limit_settings--{{ $uri }}">
		<div class="control_block">
			{{ Form::label($uri . '-limit_length', 'Limit To', array('class' => 'label--block')) }}
			{{ Form::input('text', $uri . '-limit_length', isset($settings['limit_length']) ? $settings['limit_length'] : null, array('class' => 'input__text', 'placeholder' => 'e.g., 100')) }}
		</div>
		<div class="control_block">
			{{ Form::select($uri . '-limit_type', $limit_types, isset($settings['limit_type']) ? $settings['limit_type'] : null, array('class' => 'select')) }}
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