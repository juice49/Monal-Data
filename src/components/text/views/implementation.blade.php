<div class="control_block">
	<div class="control_block">
		{{ Form::label($uri . '-text', 'Content', array('class' => 'label label--block')) }}
		@if (isset($settings['type']))
			@if ($settings['type'] === 'single-line')
				{{ Form::input('text' , $uri . '-text', isset($values['text']) ? $values['text'] : null, array('class' => 'js--text--' . $uri . ' input__text')) }}
			@elseif ($settings['type'] === 'block')
				{{ Form::textarea($uri . '-text', isset($values['text']) ? $values['text'] : null, array('class' => 'js--text--' . $uri . ' textarea')) }}
			@endif
		@endif
		@if (
			isset($settings['limit']) AND
			$settings['limit'] AND
			isset($settings['limit_length']) AND
			isset($settings['limit_type'])
		)
			<label for="{{ $uri }}-text" class="label label--block label--description">
				<span class="js--word_count--{{ $uri }}">{{ $settings['limit_length'] }}</span> / {{ $settings['limit_length'] }} {{ $settings['limit_type'] }} remaining
			</label>
		@endif
	</div>
</div>
@if (
	isset($settings['limit']) AND
	$settings['limit'] AND
	isset($settings['limit_length']) AND
	isset($settings['limit_type'])
)
	<script>
		(function(window, jQuery){
			$('.js--text--{{ $uri }}').on('keyup', function(){
				count($(this).val());
			});
			$('.js--text--{{ $uri }}').on('blur', function(){
				count($(this).val());
			});

			function count(str) {
				var count = 0;
				if (str !== '') {
					@if ($settings['limit_type'] === 'characters')
						count = str.length;
					@elseif ($settings['limit_type'] === 'words')
						text = $.trim(str);
						text = $.trim(text);
						text = text.replace(/\s{2,}/g, ' ');
						text = text.replace(/\n /, '\n');
						count = text.split(' ').length;
					@endif
				}
				var remaining = parseInt({{ $settings['limit_length'] }}) - count;
				$('.js--word_count--{{ $uri }}').html(remaining);
			}

			count($('.js--text--{{ $uri }}').val());
		})(window, jQuery)
	</script>
@endif