(function(window, $){

	'use strict';

	window.components = {};

	window.components.storedTemplateViews = {};

	window.components.create = function(){

	};

	window.components.temaplteView = function(type, uri, success){
		if (typeof components.storedTemplateViews[type] === 'undefined'){
			$.ajax({
				type: 'POST',
				url: _APP_BASEURL + 'ajax',
				dataType: 'json',
				data: {
					_use: 'DataSetTemaplatesAJAXController@componentTemplate',
					type: type,
					uri: uri
				},
				success : function(results){
					if (results.status == 'OK'){
						components.storedTemplateViews[type] = results.view;
						success(results.view);
					}
				}
			});
		}
		else{
			success(components.storedTemplateViews[type]);
		}
	}

})(window, jQuery);