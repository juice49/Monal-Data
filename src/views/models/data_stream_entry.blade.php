@foreach ($data_sets as $data_set)
	{{ $data_set->view(array(
		'show_validation' => $show_validation
	)) }}
@endforeach