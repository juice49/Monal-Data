@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">Data Stream Templates</h1>
@stop
@section('body-content')

	@if($system_user->hasAdminPermissions('data_stream_templates', 'create_data_stream_template'))
		<div class="align--right">
			<a href="{{ URL::route('admin.data-stream-templates.create') }}" class="button button--wasabi">Create data stream template</a>
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
			@foreach ($data_stream_templates as $data_stream_template)
				<div class="tile">
					<div class="tile__content">
						<table class="tile__table">
							<tbody>
								<tr>
									<td><span class="tile__table--row_title">Name:</span></td>
									<td>{{ $data_stream_template->name() }}</td>
								</tr>
							</tbody>
						</table>
						<div class="node__y--top align--right">
							@if($system_user->hasAdminPermissions('data_stream_templates', 'edit_data_stream_template'))
								<a href="{{ URL::route('admin.data-stream-templates.edit', $data_stream_template->ID()) }}" class="button button--small button--dusk">Edit</a>
							@endif
							@if($system_user->hasAdminPermissions('data_stream_templates', 'delete_data_stream_template'))
								<span class="button button--small button--cuban_heat">Delete</span>
							@endif
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>

@stop