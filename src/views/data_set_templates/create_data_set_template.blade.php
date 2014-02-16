@extends('../dashboard')
@section('master-head')
	<script src="{{ URL::to('packages/fruitful/data/js/components.js') }}"></script>
	<script src="{{ URL::to('packages/fruitful/data/js/datasets.js') }}"></script>
@stop
@section('body-header')
	<h1 class="color--teal">Create Data Set Template</h1>
@stop
@section('body-content')

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

	{{ Form::open() }}
		{{ $instance_view }}
		<div class="form__controls form__controls--standard control_block">
			<div class="form__controls__left">
				<a href="{{ URL::route('admin.data-set-templates') }}" class="button button--mustard">Cancel</a>
			</div>
			<div class="form__controls__right align--right">
				{{ Form::submit('Create', array('class' => 'button button--wasabi')) }}
			</div>
		</div>
	{{ Form::close() }}

@stop