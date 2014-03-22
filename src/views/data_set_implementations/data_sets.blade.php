@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">Data Sets</h1>
@stop
@section('body-content')

@if($system_user->hasAdminPermissions('data_sets', 'create_data_set'))
		<div class="align--right">
			<a href="{{ URL::route('admin.data-sets.create.choose') }}" class="button button--wasabi">Create data set</a>
		</div>
	@endif

	<div class="node__y--top navbar">
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
			@foreach ($data_sets as $data_set)
				<li class="tile">
					<div class="tile__content">
						<table class="tile__table">
							<tbody>
								<tr>
									<td><span class="tile__table--row_title">Name:</span></td>
									<td>{{ $data_set->name() }}</td>
								</tr>
							</tbody>
						</table>
						<div class="node__y--top align--right">
							@if($system_user->hasAdminPermissions('data_sets', 'edit_data_set'))
								<a href="{{ URL::route('admin.data-sets.edit', $data_set->ID()) }}" class="button button--small button--dusk">Edit</a>
							@endif
							@if($system_user->hasAdminPermissions('data_sets', 'delete_data_set'))
								<span class="button button--small button--cuban_heat">Delete</span>
							@endif
						</div>
					</div>
				</li>
			@endforeach
		</ul>
	</div>

@stop