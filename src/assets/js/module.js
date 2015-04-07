jQuery(document).ready(function(){
	jQuery('body').on('click', 'input[type=checkbox]', function(){
		var tr = jQuery(this).parents('tr:first');
		var data = {
			'checked' : jQuery(this).is(':checked')?'true':'false' 
		};
		/*
		jQuery.ajax({
			'data' : data,
			'url' :  jQuery(tr).data('url-update'),
			'success' : function(data, status, xhr) {
				jQuery(jQuery(tr).data('target')).replaceWith(data);
			}
		});
		*/
		var mode = jQuery(this).data('mode');
		if(typeof(mode) == 'undefined') {
			mode = null;
		}
		sweelix.raise('ajaxRefreshHandler', {
			'targetUrl' : jQuery(tr).data('url-update'),
			'data' : data,
			'mode' : mode,
			'targetSelector' : jQuery(tr).data('target')
		});
	});
	jQuery('body').on('click', '.ajaxRefresh', function(evt){
		evt.preventDefault();
		var mode = jQuery(this).data('mode');
		if(typeof(mode) == 'undefined') {
			mode = null;
		}
		var target = jQuery(this).data('target');
		if(typeof(target) == 'undefined') {
			target = null;
		}
		sweelix.raise('ajaxRefreshHandler', {
			'targetUrl' : jQuery(this).attr('href'),
			'mode' : mode,
			'targetSelector' : target
		})
	});
});