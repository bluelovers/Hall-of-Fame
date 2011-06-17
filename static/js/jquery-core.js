
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

			var _func = function() {
				$(this).next().fadeIn(400).delay(500, _func);
				$('#contents').scrollTop($('#contents table').first().height());
			}

			$(this).next().fadeIn(400).delay(500, _func);
		});
	}

	// 以首頁的登入表單作為判定
	if ($('#contents form :submit[name="Login"]').length > 0) {
		$('#contents > div').width('100%')
			.children('div:eq(0)').width(270).css({
				'overflow-x' : 'hidden'
			})
			.next('div').width(480)
				.children('div:eq(0)').width('100%')
				/*
					.children('img').css({
						'margin-left' : (Math.round(Math.random() * 1) ? 0 : -500),
						'margin-top' : -1 - Math.round(Math.random() * 4) * (200 + 1)
					})
				*/
			;

		$('#contents > div > div:eq(1) div img').css('margin-left', function(){
			var _this = $(this);
			var _func = function(){
				_this.css({
					'margin-left' : (Math.round(Math.random() * 1) ? 0 : -500),
					'margin-top' : -1 - Math.round(Math.random() * 4) * (200 + 1)
				});
			};

			setInterval(_func, 5000);
			_func();
		});
	}

});
