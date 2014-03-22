@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">Data Streams</h1>
@stop
@section('body-content')

	@if($system_user->hasAdminPermissions('data_streams', 'create_data_stream'))
		<div class="align--right">
			<a href="{{ URL::route('admin.data-streams.choose-template') }}" class="button button--wasabi">Create data stream</a>
		</div>
	@endif

	<div class="node__y--top navbar">
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
		<div class="wall__tiles">
			@foreach ($data_streams as $data_stream)
				<div class="tile">
					<div class="tile__content">
						<table class="tile__table">
							<tbody>
								<tr>
									<td><span class="tile__table--row_title">Name:</span></td>
									<td>{{ $data_stream->name() }}</td>
								</tr>
							</tbody>
						</table>
						<div class="node__y--top align--right">
							@if($system_user->hasAdminPermissions('data_streams', 'create_stream_entry'))
								<a href="{{ URL::route('admin.data-sets.edit', $data_stream->ID()) }}" class="button button--small button--wasabi">Add entry</a>
							@endif
							<a href="" class="button button--small button--mist">View</a>
							@if($system_user->hasAdminPermissions('data_streams', 'edit_data_stream'))
								<a href="{{ URL::route('admin.data-sets.edit', $data_stream->ID()) }}" class="button button--small button--dusk">Edit</a>
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