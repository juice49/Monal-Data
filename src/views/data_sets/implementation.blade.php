<div class="well">
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
	<div class="control_block">
		{{ Form::label($uri . '-name', 'Name', array('class' => 'label--block')) }}
		{{ Form::input('text', $uri . '-name', $name, array('class' => 'input--text')) }}
		{{ Form::input('hidden', $uri . '-data_set', $uri) }}
	</div>
	{{ $component_view }}
</div>