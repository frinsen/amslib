//	jQuery no-double-tap-zoom plugin
//	Triple-licensed: Public Domain, MIT and WTFPL license - share and enjoy!
//	chris.thomas@antimatter-studios.com: I modified this to use modernizr and the html.touch detection and also to stop counting two clicks at once, but count each click separately.
(function($) {
	$.fn.nodoubletapzoom = function() {
		if($("html.touch").length == 0) return;

		$(this).bind('touchstart', function(e){
			var t2 = e.timeStamp;
			var t1 = $(this).data('lastTouch') || t2;
			var dt = t2 - t1;
			var fingers = e.originalEvent.touches.length;
			$(this).data('lastTouch', t2);
			
			// not double-tap
			if (!dt || dt > 500 || fingers > 1){
				return;
			}
			
			// double tap - prevent the zoom
			e.preventDefault();
			// also synthesize click events we just swallowed up
			$(e.target).trigger("click");
		});
	};
})(jQuery);