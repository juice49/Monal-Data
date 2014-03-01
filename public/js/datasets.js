(function(window, $){

	'use strict';

	window.datasets = {};

	window.datasets.add = function(success){
		$.ajax({
			type: 'POST',
			url: _APP_BASEURL + 'ajax',
			dataType: 'json',
			data: {
				_use: 'DataSetsAJAXController@dataSetTemplateView'
			},
			success : function(results){
				if (results.status == 'OK'){
					success(results.view);
				}
			}
		});
	}

})(window, jQuery);