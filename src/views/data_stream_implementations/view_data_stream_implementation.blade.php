@extends('../dashboard')
@section('body-header')
	<h2 class="dashboard__subtitle">{{ $data_stream->name() }}</h2>
	<h1 class="dashboard__title">{{ $data_stream->name() }}</h1>
@stop
@section('body-content')

	<div class="node__y--top">
		<div class="wall__tiles">
			@foreach ($data_stream->entries() as $entry)
				<div class="tile">
					<div class="tile__content">
						<ul class="tile__properties">
							<?php $i = 0; ?>
							@foreach ($entry->summariseDataSets() as $key => $value)
								@if ($data_stream->hasPreviewColumn($i))
									<li class="tile__property">
										<span class="tile__property__key">{{ $key }}:</span>
										<span class="tile__property__value">{{ $value }}</span>
									</li>
								@endif
								<?php $i++; ?>
							@endforeach
						</ul>
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