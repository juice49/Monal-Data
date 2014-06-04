@extends('../dashboard')
@section('body-header')
	<h2 class="dashboard__subtitle">Data Stream Enteries</h2>
	<h1 class="dashboard__title">{{ $data_stream->name() }}</h1>
@stop
@section('body-content')

	@if($system_user->hasAdminPermissions('data_streams', 'create_stream_entry'))
		<div class="align--right">
			<a href="{{ URL::route('admin.data-streams.add-entry', $data_stream->ID()) }}" class="button button--wasabi">Add entry</a>
		</div>
	@endif

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

	{{ $data_stream->viewEnteries() }}
@stop