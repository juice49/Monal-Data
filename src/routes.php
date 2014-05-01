<?php

Fruitful::registerAdminRoute(
	'any',
	'data-sets',
	'admin.data-sets',
	'DataSetsController@dataSets'
);
Fruitful::registerAdminRoute(
	'any',
	'data-sets/create/choose',
	'admin.data-sets.create.choose',
	'DataSetsController@chooseTemplate'
);
Fruitful::registerAdminRoute(
	'any',
	'data-sets/create/{id}',
	'admin.data-sets.create',
	'DataSetsController@createDataSet'
);
Fruitful::registerAdminRoute(
	'any',
	'data-sets/edit/{id}',
	'admin.data-sets.edit',
	'DataSetsController@editDataSet'
);
Fruitful::registerAdminRoute(
	'any',
	'data-set-templates',
	'admin.data-set-templates',
	'DataSetsController@dataSetTemplates'
);
Fruitful::registerAdminRoute(
	'any',
	'data-set-templates/create',
	'admin.data-set-templates.create',
	'DataSetsController@createDataSetTemplate'
);
Fruitful::registerAdminRoute(
	'any',
	'data-set-templates/edit/{id}',
	'admin.data-set-templates.edit',
	'DataSetsController@editDataSetTemplate'
);
Fruitful::registerAdminRoute(
	'any',
	'data-streams',
	'admin.data-streams',
	'DataStreamsController@dataStreams'
);
Fruitful::registerAdminRoute(
	'any',
	'data-streams/create/choose-data-stream-template',
	'admin.data-streams.choose-template',
	'DataStreamsController@chooseDataStreamTemplate'
);
Fruitful::registerAdminRoute(
	'any',
	'data-streams/create/{id}',
	'admin.data-streams.create',
	'DataStreamsController@createDataStream'
);
Fruitful::registerAdminRoute(
	'any',
	'data-streams/edit/{id}',
	'admin.data-streams.edit',
	'DataStreamsController@editDataStream'
);
Fruitful::registerAdminRoute(
	'any',
	'data-streams/view/{id}',
	'admin.data-streams.view',
	'DataStreamsController@viewDataStream'
);
Fruitful::registerAdminRoute(
	'any',
	'data-streams/add-entry/{id}',
	'admin.data-streams.add-entry',
	'DataStreamsController@addEntry'
);
Fruitful::registerAdminRoute(
	'any',
	'data-stream-templates',
	'admin.data-stream-templates',
	'DataStreamsController@dataStreamTemplates'
);
Fruitful::registerAdminRoute(
	'any',
	'data-stream-templates/create',
	'admin.data-stream-templates.create',
	'DataStreamsController@createDataStreamTemplate'
);
Fruitful::registerAdminRoute(
	'any',
	'data-stream-templates/edit/{id}',
	'admin.data-stream-templates.edit',
	'DataStreamsController@editDataStreamTemplate'
);