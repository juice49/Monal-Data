@extends('../dashboard')
@section('body-header')
	<h1 class="color--teal">Data Set Templates</h1>
@stop
@section('body-content')

	@if($system_user->hasAdminPermissions('data_set_templates', 'create_data_set_template'))
		<div class="align--right">
			<a href="{{ URL::route('admin.data-set-templates.create') }}" class="button button--wasabi">Create data set template</a>
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

	<div class="node__y--top">
		<ul class="wall__tiles">
			@foreach ($data_set_templates as $data_set_template)
				<li class="tile">
					<div class="tile__content">
						<table class="tile__table">
							<tbody>
								<tr>
									<td><span class="tile__table--row_title">Name:</span></td>
									<td>{{ $data_set_template->name() }}</td>
								</tr>
								<tr>
									<td><span class="tile__table--row_title">Component:</span></td>
									<td>{{ $data_set_template->componentName() }}</td>
								</tr>
							</tbody>
						</table>
						<div class="node__y--top align--right">
							@if($system_user->hasAdminPermissions('data_set_templates', 'edit_data_set_template'))
								<a href="{{ URL::route('admin.data-set-templates.edit', $data_set_template->id) }}" class="button button--small button--dusk">Edit</a>
							@endif
							@if($system_user->hasAdminPermissions('data_set_templates', 'delete_data_set_template'))
								<span class="button button--small button--cuban_heat">Delete</span>
							@endif
						</div>
					</div>
				</li>
			@endforeach
		</ul>
	</div>

@stop