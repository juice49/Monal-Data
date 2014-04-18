<?php

Route::any(
	'admin/data-sets', 
	array(
		'as' => 'admin.data-sets',
		'uses' => 'DataSetsController@dataSets'
	)
);
Route::any(
	'admin/data-sets/create/choose',
	array(
		'as' => 'admin.data-sets.create.choose',
		'uses' => 'DataSetsController@chooseTemplate'
	)
);
Route::any(
	'admin/data-sets/create/{id}',
	array(
		'as' => 'admin.data-sets.create',
		'uses' => 'DataSetsController@createDataSet'
	)
);
Route::any(
	'admin/data-sets/edit/{id}',
	array(
		'as' => 'admin.data-sets.edit',
		'uses' => 'DataSetsController@editDataSet'
	)
);
Route::any(
	'admin/data-set-templates',
	array(
		'as' => 'admin.data-set-templates',
		'uses' => 'DataSetsController@dataSetTemplates'
	)
);
Route::any(
	'admin/data-set-templates/create',
	array(
		'as' => 'admin.data-set-templates.create',
		'uses' => 'DataSetsController@createDataSetTemplate'
	)
);
Route::any(
	'admin/data-set-templates/edit/{id}',
	array(
		'as' => 'admin.data-set-templates.edit',
		'uses' => 'DataSetsController@editDataSetTemplate'
	)
);
Route::any(
	'admin/data-streams',
	array(
		'as' => 'admin.data-streams',
		'uses' => 'DataStreamsController@dataStreams'
	)
);
Route::any(
	'admin/data-streams/create/choose-data-stream-template',
	array(
		'as' => 'admin.data-streams.choose-template',
		'uses' => 'DataStreamsController@chooseDataStreamTemplate'
	)
);
Route::any(
	'admin/data-streams/create/{id}',
	array(
		'as' => 'admin.data-streams.create',
		'uses' => 'DataStreamsController@createDataStream'
	)
);
Route::any(
	'admin/data-streams/edit/{id}',
	array(
		'as' => 'admin.data-streams.edit',
		'uses' => 'DataStreamsController@editDataStream'
	)
);
Route::any(
	'admin/data-streams/add-entry/{id}',
	array(
		'as' => 'admin.data-streams.add-entry',
		'uses' => 'DataStreamsController@addEntry'
	)
);
Route::any(
	'admin/data-stream-templates',
	array(
		'as' => 'admin.data-stream-templates',
		'uses' => 'DataStreamsController@dataStreamTemplates'
	)
);
Route::any(
	'admin/data-stream-templates/create',
	array(
		'as' => 'admin.data-stream-templates.create',
		'uses' => 'DataStreamsController@createDataStreamTemplate'
	)
);
Route::any(
	'admin/data-stream-templates/edit/{id}',
	array(
		'as' => 'admin.data-stream-templates.edit',
		'uses' => 'DataStreamsController@editDataStreamTemplate'
	)
);