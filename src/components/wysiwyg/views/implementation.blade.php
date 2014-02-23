<div class="control_block">
	<div class="control_block">
		{{ Form::label($uri . '-wysiwyg', 'Content', array('class' => 'label--block')) }}
		{{ Form::textarea($uri . '-wysiwyg', isset($values['wysiwyg']) ? $values['wysiwyg'] : null) }}
	</div>
</div>

<script>
(function(window, jQuery){
	$('textarea#{{ $uri . '-wysiwyg' }}').redactor({
		{{ $wysiwyg_settings }}
		buttonSource : true
	});
})(window, jQuery)
</script>