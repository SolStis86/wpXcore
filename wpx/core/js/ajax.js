(function($){

$.wpxAJAX = function(method, data, success, error) {
			var setup = { action: method, nonce: WPX.nonce },
				ajaxData = $.extend(setup, data);
				var ajaxWrapper = $('#ajax-active');
				var ajaxSpinner = $('#ajax-active .spinner');
				var ajaxMessage = $('#ajax-active .ajax-message');
				//console.log(WPX.ajaxurl);
			$.ajax({
				url: WPX.ajaxurl,
				type: 'POST',
				data: ajaxData,
				beforeSend: function() {
					ajaxSpinner.show();
					ajaxMessage.html('').hide();
					ajaxWrapper.fadeIn('slow');
				},
				success: function(data) {

					success(data);
					
				},
				error: function(data){
					console.log(data)
					error;
				},
				complete: function() {
					ajaxWrapper.fadeOut('slow');
				}
			});
		};

	
})(jQuery);
