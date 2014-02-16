@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">Data Sets</h1>
@stop
@section('body-content')

	<div class="navbar">
		<ul class="navbar__menu navbar__menu--dusk">
			<li><a href="{{ URL::route('admin.data-sets') }}" class="navbar__menu__link">Data Sets</a></li>
			<li><a href="{{ URL::route('admin.data-set-templates') }}" class="navbar__menu__link">Data Set Templates</a></li>
		</ul>
	</div>

	@if ($messages)
		<div class="node__y--top">
			@if ($messages->has('success'))
				<div class="message_box message_box--wasabi">
					<h6>Woot!</h6>
					<ul>
						@foreach($messages->all() as $message)
							<li>{{ $message }}</li>
						@endforeach
					</ul>
				</div>
			@else
				<div class="message_box message_box--tomato">
					<h6>ERROR</h6>
					<ul>
						@foreach($messages->all() as $message)
							<li>{{ $message }}</li>
						@endforeach
					</ul>
				</div> 
			@endif
		</div>
	@endif

@stop