// header waterfall
	$(function () {
		'use strict';

		$('.header-waterfall').each(function () {
			$(this).affix({
				offset: {
					top: 1
				}
			});
		});
	});
