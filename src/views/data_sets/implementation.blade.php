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
	@if ($modify_name)
		<div class="control_block">
			{{ Form::label($uri . '-name', 'Name', array('class' => 'label--block')) }}
			{{ Form::input('text', $uri . '-name', $name, array('class' => 'input--text')) }}
		</div>
	@else
		@if ($name AND $name != '')
			<h2>{{ $name }}</h2>
		@endif
	@endif
	{{ $component_view }}
	{{ Form::input('hidden', $uri . '-data_set', $uri) }}
</div>