(function( $ ) {
	'use strict';

	$(document).ready(function($) {
		
		$('.gmap[data-mode="minheight"][data-minheight]').each(function(){
			var _this = $(this),
				_height = _this.data('minheight');
				
			_this.css({
				'height': _height + 'px'
			});
		});
		
	});

})( jQuery );
