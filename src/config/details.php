<?php

return array(
	'name' => 'Data Sets',
	'has_backend' => true,
	'control_panel_menu' => array(
		'Data' => array(
			'Data Sets' => array(
				'route' => '/data-sets',
				'permissions' => 'data-sets'
				),
			),
		),
	'permission_sets' => array(
		'Data Sets' => array(
			'set_slug' => 'data-sets',
			'set_permissions' => array(
				'Create Data Set' => 'create_data_set',
				'Edit Data Set' =>'edit_data_set',
				'Delete Data Set' =>'delete_data_set',
				),
			),
		),
	);