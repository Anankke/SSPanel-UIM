// snackbar
	(function ($) {
		'use strict';

		var Snackbar = function (options) {
			this.options  = options;
			this.$element = $('<div class="snackbar-inner">' + this.options.content + '</div>');
		};

		Snackbar.DEFAULTS = {
			alive: 6000,
			content: '&nbsp;',
			hide: function () {},
			show: function () {}
		};

		Snackbar.prototype.fbtn = function (margin) {
			if ($(window).width() < 768 && $('.fbtn-container').length) {
				var str = 'translateY(-' + margin + 'px)';
				$('.fbtn-container').css({
					'-webkit-transform': str,
					'transform': str
				});
			};
		};

		Snackbar.prototype.hide = function () {
			var that = this;

			this.$element.removeClass('in');

			clearTimeout(this.$element.data('timer'));

			if ($.support.transition) {
				this.$element.one('bsTransitionEnd', function () {
					that.options.hide(that.options);
					that.$element.remove();
				});
			} else {
				that.options.hide(that.options);
				that.$element.remove();
			}

			this.fbtn('0');
		};

		Snackbar.prototype.show = function () {
			var that = this;

			if (!$('.snackbar').length) {
				$(document.body).append('<div class="snackbar"></div>');
			};

			this.$element.appendTo('.snackbar').show().addClass(function () {
				that.$element.on('click', '[data-dismiss="snackbar"]', function () {
					that.hide();
				});

				that.$element.data('timer', setTimeout(function () {
					that.hide();
				}, that.options.alive));

				that.$element.on('mouseenter', function () {
					clearTimeout(that.$element.data('timer'));
				}).on('mouseleave', function () {
					that.$element.data('timer', setTimeout(function () {
						that.hide();
					}, that.options.alive));
				});

				that.options.show(that.options);

				return 'in';
			});

			this.fbtn(this.$element.outerHeight());
		};

		function Plugin (option) {
			return this.each(function () {
				var $this    = $(document.body);
				var data     = $this.data('bs.snackbar');
				var options  = $.extend({}, Snackbar.DEFAULTS, option);

				if (!data) {
					$this.data('bs.snackbar', (data = new Snackbar(options)));
					data.show();
				} else if ($('.snackbar-inner').length && !$('.snackbar-inner.old').length) {
					$('.snackbar-inner.in').addClass('old')
					data.hide();
					if ($.support.transition) {
						$(document).one('bsTransitionEnd', '.snackbar-inner.old', function () {
							$this.data('bs.snackbar', (data = new Snackbar(options)));
							data.show();
						});
					} else {
						$this.data('bs.snackbar', (data = new Snackbar(options)));
						data.show();
					};
				} else if (!$('.snackbar-inner').length) {
					$this.data('bs.snackbar', (data = new Snackbar(options)));
					data.show();
				};
			});
		};

		var old = $.fn.snackbar;

		$.fn.snackbar             = Plugin;
		$.fn.snackbar.Constructor = Snackbar;

		$.fn.snackbar.noConflict = function () {
			$.fn.snackbar = old;
			return this;
		};
	}(jQuery));
