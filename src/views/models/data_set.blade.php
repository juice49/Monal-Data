<div class="well">
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
	@if ($modify_name)
		<div class="control_block">
			{{ Form::label($uri . '-name', 'Name', array('class' => 'label label--block')) }}
			{{ Form::input('text', $uri . '-name', $name, array('class' => 'input__text')) }}
		</div>
	@else
		@if ($name AND $name != '')
			<h2>{{ $name }}</h2>
		@endif
	@endif
	{{ $component_view }}
	{{ Form::input('hidden', $uri . '-data_set', $uri) }}
</div>