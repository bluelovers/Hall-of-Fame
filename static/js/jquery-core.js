
//$$ = jQuery.noConflict();

( function($) {

} ) ( jQuery );

jQuery(function($){

	// 檢查是否為戰鬥訊息
	if ($('.btl_img').length > 0) {

		$(window).resize(function(){
			$('#contents').css('height',
				$(window).height()
				- ($('#main_frame').outerHeight(true) - $('#main_frame').height())
				- ($('#contents').outerHeight(true) - $('#contents').height())
				- $('#title').outerHeight(true)
				- $('#menu').outerHeight(true)
				- $('#menu2').outerHeight(true)
				- $('#foot').outerHeight(true)
			);
		}).resize();

		$('#contents').css({
			'min-height' : $('.btl_img').parent('tr').outerHeight(true),
			'overflow' : 'auto'
		}).find('table:first tr').hide().first().fadeIn(400).delay(500, function (){
			$('#contents').scrollTop($('#contents table').first().height());

			_func = function() {
				$(this).next().fadeIn(400).delay(500, _func);
				$('#contents').scrollTop($('#contents table').first().height());
			}

			$(this).next().fadeIn(400).delay(500, _func);
		});
	}

});
