@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">Data Streams</h1>
@stop
@section('body-content')

	@if($system_user->hasAdminPermissions('data_streams', 'create_data_stream'))
		<div class="align--right">
			<a href="{{ URL::route('admin.data-sets.create.choose') }}" class="button button--wasabi">Create data stream</a>
		</div>
	@endif

	<div class="navbar">
		<ul class="navbar__menu navbar__menu--dusk">
			<li><a href="{{ URL::route('admin.data-streams') }}" class="navbar__menu__link">Data Streams</a></li>
			<li><a href="{{ URL::route('admin.data-stream-templates') }}" class="navbar__menu__link">Data Stream Templates</a></li>
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
					<h6>Great Scott!</h6>
					<ul>
						@foreach($messages->all() as $message)
							<li>{{ $message }}</li>
						@endforeach
					</ul>
				</div> 
			@endif
		</div>
	@endif

	<div class="node__y--top">
		<ul class="wall__tiles">
		</ul>
	</div>

@stop