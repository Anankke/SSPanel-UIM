// menu
	(function ($) {
		'use strict';

		var Menu = function (element, options) {
			this.ignoreBackdropClick = false;
			this.isShown             = null;
			this.options             = options;
			this.originalBodyPad     = null;
			this.scrollbarWidth      = 0;
			this.$backdrop           = null;
			this.$body               = $(document.body);
			this.$element            = $(element);
			this.$dialog             = this.$element.find('.menu-scroll');
		};

		if (!$.fn.modal) {
			throw new Error('Menu requires Bootstrap modal.js');
		};

		Menu.DEFAULTS = $.extend({}, $.fn.modal.Constructor.DEFAULTS, {});
		Menu.TRANSITION_DURATION = 300;
		Menu.TRANSITION_DURATION_BACKDROP = 150;

		Menu.prototype = $.extend({}, $.fn.modal.Constructor.prototype);

		Menu.prototype.backdrop = function (callback) {
			var that = this;

			if (this.isShown && this.options.backdrop) {
				var doAnimate = $.support.transition;

				this.$backdrop = $(document.createElement('div')).addClass('menu-backdrop').appendTo(this.$body);

				this.$element.on('click.dismiss.bs.menu', $.proxy(function (e) {
					if (this.ignoreBackdropClick) {
						this.ignoreBackdropClick = false;
						return;
					};

					if (e.target !== e.currentTarget) {
						return;
					};

					this.options.backdrop == 'static' ? this.$element[0].focus() : this.hide();
				}, this));

				if (doAnimate) {
					this.$backdrop[0].offsetWidth;
				};

				this.$backdrop.addClass('in');

				if (!callback) {
					return;
				};

				doAnimate ? this.$backdrop.one('bsTransitionEnd', callback).emulateTransitionEnd(Menu.TRANSITION_DURATION_BACKDROP) : callback();
			} else if (!this.isShown && this.$backdrop) {
				this.$backdrop.removeClass('in');

				var callbackRemove = function () {
					that.removeBackdrop();
					callback && callback();
				};

				$.support.transition ? this.$backdrop.one('bsTransitionEnd', callbackRemove).emulateTransitionEnd(Menu.TRANSITION_DURATION_BACKDROP) : callbackRemove();
			} else if (callback) {
				callback();
			};
		};

		Menu.prototype.hide = function (e) {
			if (e) e.preventDefault();

			e = $.Event('hide.bs.menu');

			this.$element.trigger(e);

			if (!this.isShown || e.isDefaultPrevented()) {
				return;
			};

			this.isShown = false;

			this.escape();

			$(document).off('focusin.bs.modal');

			this.$element.removeClass('in').off('click.dismiss.bs.menu').off('mouseup.dismiss.bs.menu');

			this.$dialog.off('mousedown.dismiss.bs.menu');

			$.support.transition ? this.$element.one('bsTransitionEnd', $.proxy(this.hideModal, this)).emulateTransitionEnd(Menu.TRANSITION_DURATION) : this.hideModal();
		};

		Menu.prototype.hideModal = function () {
			var that = this;

			this.$element.hide();

			this.backdrop(function () {
				that.$element.trigger('hidden.bs.menu');
			});
		};

		Menu.prototype.show = function (_relatedTarget) {
			var that = this;
			var e    = $.Event('show.bs.menu', { relatedTarget: _relatedTarget });

			this.$element.trigger(e);

			if (this.isShown || e.isDefaultPrevented()) {
				return;
			};

			this.isShown = true;

			this.escape();

			this.$element.on('click.dismiss.bs.menu', '[data-dismiss="menu"]', $.proxy(this.hide, this));

			this.$dialog.on('mousedown.dismiss.bs.menu', function () {
				that.$element.one('mouseup.dismiss.bs.menu', function (e) {
					if ($(e.target).is(that.$element)) {
						that.ignoreBackdropClick = true;
					};
				});
			});

			this.backdrop(function () {
				var transition = $.support.transition;

				if (!that.$element.parent().length) {
					that.$element.appendTo(that.$body);
				};

				that.$element.show();

				if (transition) {
					that.$element[0].offsetWidth;
				};

				that.$element.addClass('in');

				that.enforceFocus();

				var e = $.Event('shown.bs.menu', { relatedTarget: _relatedTarget });

				transition ? that.$dialog.one('bsTransitionEnd', function () {
					that.$element.trigger('focus').trigger(e);
				}).emulateTransitionEnd(Menu.TRANSITION_DURATION) : that.$element.trigger('focus').trigger(e);
			});
		};

		function Plugin (option, _relatedTarget) {
			return this.each(function () {
				var $this   = $(this);
				var data    = $this.data('bs.menu');
				var options = $.extend({}, Menu.DEFAULTS, $this.data(), typeof option == 'object' && option);

				if (!data) $this.data('bs.menu', (data = new Menu(this, options)));
				if (typeof option == 'string') data[option](_relatedTarget);
				else if (options.show) data.show(_relatedTarget);
			});
		};

		var old = $.fn.menu;

		$.fn.menu             = Plugin;
		$.fn.menu.Constructor = Menu;

		$.fn.menu.noConflict = function () {
			$.fn.menu = old;
			return this;
		};

		$(document).on('click.bs.menu.data-api', '[data-toggle="menu"]', function (e) {
			var $this   = $(this);
			var href    = $this.attr('href');
			var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, '')));
			var option  = $target.data('bs.menu') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data());

			if ($this.is('a')) e.preventDefault();

			$target.one('show.bs.menu', function (showEvent) {
				if (showEvent.isDefaultPrevented()) {
					return;
				} else {
					$target.attr('tabindex', '-1');
				};

				$target.one('hidden.bs.menu', function () {
					$this.is(':visible') && $this.trigger('focus');
				});
			});

			Plugin.call($target, option, this);
		});
	}(jQuery));
