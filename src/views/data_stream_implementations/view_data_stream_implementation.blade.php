@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">{{ $data_stream->name() }}</h1>
@stop
@section('body-content')

	<div class="node__y--top">
		<div class="wall__tiles">
			@foreach ($data_stream->entries() as $entry)
				<div class="tile">
					<div class="tile__content">
						<table class="tile__table">
							<tbody>
								<?php $i = 0; ?>
								@foreach ($entry->summariseDataSets()  as $key => $value)
									@if ($data_stream->hasPreviewColumn($i))
										<tr>
											<td><span class="tile__table--row_title">{{ $key }}</span></td>
											<td>{{ $value }}</td>
										</tr>
									@endif
									<?php $i++; ?>
								@endforeach
							</tbody>
						</table>
						<div class="node__y--top align--right">
							@if($system_user->hasAdminPermissions('data_streams', 'edit_stream_entry'))
								<a href="" class="button button--small button--dusk">Edit entry</a>
							@endif
							@if($system_user->hasAdminPermissions('data_streams', 'delete_stream_entry'))
								<a href="" class="button button--small button--cuban_heat">Delete entry</a>
							@endif
						</div>
					</div>
				</div>
			@endforeach
		</div>
	</div>

@stop