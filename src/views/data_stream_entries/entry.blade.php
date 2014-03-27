@foreach ($data_sets as $data_set)
	{{ $data_set->view($show_validation_messages) }}
@endforeach