@extends('../dashboard')
@section('body-header')
	<h1 class="dashboard__title">Data Set Templates</h1>
@stop
@section('body-content')

	@if($system_user->hasAdminPermissions('data_set_templates', 'create_data_set_template'))
		<div class="align--right">
			<a href="{{ URL::route('admin.data-set-templates.create') }}" class="button button--wasabi">Create data set template</a>
		</div>
	@endif

	<nav class="node__y--top navbar">
		<ul class="navbar__menu navbar__menu--dusk">
			<li class="navbar__menu__option"><a href="{{ URL::route('admin.data-sets') }}" class="navbar__menu__link">Data Sets</a></li>
			<li class="navbar__menu__option"><a href="{{ URL::route('admin.data-set-templates') }}" class="navbar__menu__link">Data Set Templates</a></li>
		</ul>
	</nav>

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
		<ul class="wall__tiles">
			@foreach ($data_set_templates as $data_set_template)
				<li class="tile">
					<div class="tile__content">
						<ul class="tile__properties">
							<li class="tile__property">
								<span class="tile__property__key">Name:</span>
								<span class="tile__property__value">{{ $data_set_template->name() }}</span>
							</li>
							<li class="tile__property">
								<span class="tile__property__key">Component:</span>
								<span class="tile__property__value">{{ $data_set_template->componentName() }}</span>
							</li>
						</ul>
						<div class="node__y--top align--right">
							@if($system_user->hasAdminPermissions('data_set_templates', 'edit_data_set_template'))
								<a href="{{ URL::route('admin.data-set-templates.edit', $data_set_template->ID()) }}" class="button button--small button--dusk">Edit</a>
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