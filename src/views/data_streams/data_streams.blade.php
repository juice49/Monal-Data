@extends('../dashboard')
@section('body-header')
	<h1 class="dashboard__title">Data Streams</h1>
@stop
@section('body-content')

	@if($system_user->hasAdminPermissions('data_streams', 'create_data_stream'))
		<div class="align--right">
			<a href="{{ URL::route('admin.data-streams.choose-template') }}" class="button button--wasabi">Create data stream</a>
		</div>
	@endif

	<div class="node__y--top navbar">
		<ul class="navbar__menu navbar__menu--dusk">
			<li class="navbar__menu__option"><a href="{{ URL::route('admin.data-streams') }}" class="navbar__menu__link">Data Streams</a></li>
			<li class="navbar__menu__option"><a href="{{ URL::route('admin.data-stream-templates') }}" class="navbar__menu__link">Data Stream Templates</a></li>
		</ul>
	</div>

	@if ($messages)
		<div class="node__y--top">
			@if ($messages->has('success'))
				<div class="message_box message_box--wasabi">
					<span class="message_box__title">Woot!</span>
					<ul>
						@foreach($messages->all() as $message)
							<li>{{ $message }}</li>
						@endforeach
					</ul>
				</div>
			@else
				<div class="message_box message_box--tomato">
					<span class="message_box__title">Great Scott!</span>
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
		<div class="wall__tiles">
			@foreach ($data_streams as $data_stream)
				<div class="tile">
					<div class="tile__content">
						<ul class="tile__properties">
							<li class="tile__property">
								<span class="tile__property__key">Name:</span>
								<span class="tile__property__value">{{ $data_stream->name() }}</span>
							</li>
						</ul>
						<div class="node__y--top align--right">
							@if($system_user->hasAdminPermissions('data_streams', 'create_stream_entry'))
								<a href="{{ URL::route('admin.data-streams.add-entry', $data_stream->ID()) }}" class="button button--small button--wasabi">Add entry</a>
							@endif
							<a href="{{ URL::route('admin.data-streams.view', $data_stream->ID()) }}" class="button button--small button--mist">View</a>
							@if($system_user->hasAdminPermissions('data_streams', 'edit_data_stream'))
								<a href="{{ URL::route('admin.data-streams.edit', $data_stream->ID()) }}" class="button button--small button--dusk">Edit</a>
							@endif
							@if($system_user->hasAdminPermissions('data_streams', 'delete_data_stream'))
								<span class="button button--small button--cuban_heat">Delete</span>
							@endif
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>

@stop