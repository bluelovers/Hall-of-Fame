//$$ = jQuery.noConflict();

(function($) {

})(jQuery);

jQuery(function($) {

	function isTouchDevice() {

		//return true;

		try {
			document.createEvent("TouchEvent");
			return true;
		} catch (e) {
			return false;
		}
	}

	function touchScroll(id) {
		if (isTouchDevice()) { //if touch events exist...
			var el = document.getElementById(id);
			var scrollStartPos = 0;

			document.getElementById(id).addEventListener("touchstart", function(event) {
				scrollStartPos = this.scrollTop + event.touches[0].pageY;
				event.preventDefault();
			}, false);

			document.getElementById(id).addEventListener("touchmove", function(event) {
				this.scrollTop = scrollStartPos - event.touches[0].pageY;
				event.preventDefault();
			}, false);
		}
	}

	setTimeout(function() {
		// Hide the address bar!
		window.scrollTo(0, 1);
	}, 0);

	//touchScroll('contents');

	// 檢查是否為戰鬥訊息
	if ($('.btl_img').length > 0) {

		$('.ttd2, .ttd1').width('50%');



		if (!isTouchDevice()) {

			$('#foot').css({
				'position': 'absolute',
				'bottom': 0
			});

			$('#contents').css('padding-bottom', 0);
			$('#contents').css('margin-bottom', $('#foot').outerHeight(true) + 1);

			$(window).resize(function() {
				$('#contents').css('height', $(window).height() - ($('#main_frame').outerHeight(true) - $('#main_frame').height()) - ($('#contents').outerHeight(true) - $('#contents').height()) - $('#title').outerHeight(true) - $('#menu').outerHeight(true) - $('#menu2').outerHeight(true)
				//	- $('#foot').outerHeight(true)
				);
			}).trigger('resize');

			$('#contents').css({
				'min-height': $('.btl_img').parent('tr').outerHeight(true),
				'overflow': 'auto'
			}).find('table.battle_frame tr').hide().first().fadeIn(700, function() {
				$(this).delay(700, function() {
					var _d = 700;

					var _top = function()
					{
						if ($('.carpet_frame').size())
						{
							return $('#contents table.battle_frame').height() - $('#contents').height();
						}
						else
						{
							return $('#contents').height() + $('#contents table.battle_frame').height();
						}
					};

					var _func = function() {

						var n = $(this).next();



						if (n.size()) {
							$(this).next().fadeIn(700, function() {
								$(this).delay(_d, _func)
							});

							$('#contents').scrollTop(_top());
						} else {
							$(window).scrollTop(_top() + 200);
						}
					};

					$(this).next().fadeIn(700, function() {
						$(this).delay(_d, _func);
					});

					$('#contents').scrollTop(0);
				})
			});


		} else {
			$('#contents').css({
				'min-height': $('.btl_img').parent('tr').outerHeight(true),
			}).find('table.battle_frame tr').hide().first().fadeIn(700, function() {
				$(this).delay(700, function() {
					var _d = 700;
					var _func = function() {

						var n = $(this).next();

						if (n.size()) {
							$(this).next().fadeIn(700, function() {
								$(this).delay(_d, _func)
							});

							$(window).scrollTop($('#foot').offset().top - $(window).height() + 5);
						} else {
							$(window).scrollTop($(window).height());
						}
					}

					$(this).next().fadeIn(700, function() {
						$(this).delay(_d, _func)
					});
				})
			});
		}
	}

	// 以首頁的登入表單作為判定
	if ($('#contents form :submit[name="Login"]').length > 0) {
		$('#contents > div').width('100%').children('div:eq(0)').width(270).css({
			'overflow-x': 'hidden'
		}).next('div').width(480).children('div:eq(0)').width('100%').children('img').css('margin-left', function() {
			var _this = $(this);
			var _func = function() {
				_this.css({
					'margin-left': (Math.round(Math.random() * 1) ? 0 : -500),
					'margin-top': -1 - Math.round(Math.random() * 4) * (200 + 1)
				});
			};

			_func();
			setInterval(_func, 5000);

			return _this.css('margin-left');
		});
	}

	$('td[valign]')
		.each(function()
		{
			var _this = $(this);

			_this
				.css('vertical-align', _this.attr('valign'))
			;
		})
	;

});