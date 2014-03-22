(function(window, $){

	'use strict';

	window.components = {};

	window.components.temaplteView = function(type, uri, success){
		$.ajax({
			type: 'POST',
			url: _APP_BASEURL + 'ajax',
			dataType: 'json',
			data: {
				_use: 'ComponentsAJAXController@componentTemplate',
				type: type,
				uri: uri
			},
			success : function(results){
				if (results.status == 'OK'){
					success(results.view);
				}
			}
		});
	}

})(window, jQuery);