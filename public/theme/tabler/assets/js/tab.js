// tab switch
	(function ($) {
		'use strict';

		$.fn.tabSwitch = function (oldTab) {
			var $this = $(this),
			    $thisNav = $this.closest('.tab-nav'),
			    $thisNavIndicator = $('.tab-nav-indicator', $thisNav),
			    thisLeft = $this.offset().left,
			    thisNavLeft = $thisNav.offset().left,
			    thisNavWidth = $thisNav.outerWidth();

			if (oldTab !== undefined && oldTab[0] !== undefined) {
				var oldTabLeft = oldTab.offset().left;

				$thisNavIndicator.css({
					left: (oldTabLeft - thisNavLeft),
					right: (thisNavLeft + thisNavWidth - oldTabLeft - oldTab.outerWidth())
				});

				if (oldTab.offset().left > thisLeft) {
					$thisNavIndicator.addClass('reverse');

					$thisNavIndicator.one('webkitTransitionEnd oTransitionEnd msTransitionEnd transitionend', function () {
						$thisNavIndicator.removeClass('reverse');
					});
				};
			};

			$thisNavIndicator.addClass('animate').css({
				left: (thisLeft - thisNavLeft),
				right: (thisNavLeft + thisNavWidth - thisLeft - $this.outerWidth())
			}).one('webkitTransitionEnd oTransitionEnd msTransitionEnd transitionend', function () {
				$thisNavIndicator.removeClass('animate');
			});

			return this;
		}
	})(jQuery);

	$(function () {
		'use strict';

		$('.tab-nav').each(function () {
			$(this).append('<div class="tab-nav-indicator"></div>');
		});

		$(document).on('show.bs.tab', '.tab-nav a[data-toggle="tab"]', function (e) {
			$(e.target).tabSwitch($(e.relatedTarget));
		});
	});
