@extends('../dashboard')
@section('body-header')
	<h1 class="dashboard__title">Edit Data Stream</h1>
@stop
@section('body-content')
	{{ Form::open() }}
		{{ $data_stream->viewSettings() }}
		<div class="form__controls form__controls--standard control_block">
			<div class="form__controls__left">
				<a href="{{ URL::route('admin.data-streams') }}" class="button button--mustard">Cancel</a>
			</div>
			<div class="form__controls__right align--right">
				{{ Form::submit('Update', array('class' => 'button button--wasabi')) }}
			</div>
		</div>
	{{ Form::close() }}
@stop