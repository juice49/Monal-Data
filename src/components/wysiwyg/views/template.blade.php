<div class="control_block">
	<div class="control_block">
		{{ Form::label($uri . '-type', 'Settings', array('class' => 'label--block')) }}
		{{ Form::select($uri . '-type', $options, isset($settings['type']) ? $settings['type'] : null, array('class' => 'js--type--' . $uri . ' select')) }}
	</div>
	<div class="js--custom_settings--{{ $uri }} control_block">
		{{ Form::label($uri . '-custom_settings', 'Custom Settings', array('class' => 'label--block')) }}
		<label for="{{ $uri . '-custom_settings' }}" class="label--block label--description">See <a href="http://imperavi.com/redactor/docs/settings" target="_blank">Redactor’s documentation</a> for information on available settings.
</label>
		{{ Form::textarea($uri . '-custom_settings', $custom_settings, array('class' => 'textarea')) }}
	</div>
</div>
<script>
(function(window, $){

	'use strict';

	$(document).ready(function(){

		if ($('.js--type--{{ $uri }}').val() !== 'Custom'){
			$('.js--custom_settings--{{ $uri }}').hide();
		}

		$('.js--type--{{ $uri }}').on('change', function(){
			if ($(this).val() === 'Custom'){
				$('.js--custom_settings--{{ $uri }}').slideDown(200);
			}
			else{
				$('.js--custom_settings--{{ $uri }}').slideUp(200);
			}
		});
	});
})(window, jQuery);
</script>