<div class="js--data_set_template--{{ $uri }} well">
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
	<div class="control_block">
		{{ Form::label($uri . '-name', 'Name', array('class' => 'label label--block')) }}
		{{ Form::input('text', $uri . '-name', $name, array('class' => 'input__text')) }}
	</div>
	@if ($component_chooseable)
		<div class="control_block">
			{{ Form::label($uri . '-component', 'Component Type', array('class' => 'label label--block')) }}
			{{ Form::select($uri . '-component', $components, $component, array('class' => 'js--component__type--' . $uri . ' select')) }}
		</div>
	@endif
	<div class="js--component--{{ $uri }}">
		{{ $component_view }}
	</div>
	@if ($removable)
		<div class="align--right">
			<span class="js--removable--{{ $uri }} button button--cuban_heat">Remove data set</span>
		</div>
	@endif
</div>
@if ($component_chooseable)
	<script>
	(function(window, $){
		'use strict';
		$(document).ready(function(){
			@if ($component_chooseable)
				$('.js--component__type--{{ $uri }}').on('change', function(){
					if ($(this).val() != 0){
						components.temaplteView($(this).val(), '{{ $uri }}', function(view){
							$('.js--component--{{ $uri }}').html(view);
						});
					}
					else{
						$('.js--component--{{ $uri }}').html('');
					}
				});
			@endif
			@if ($removable)
				$('.js--removable--{{ $uri }}').on('click', function(){
					$('.js--data_set_template--{{ $uri }}').remove();
				});
			@endif
		});
	})(window, jQuery);
	</script>
@endif