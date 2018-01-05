// tile
	(function ($) {
		'use strict';

		var Tile = function (element, options) {
			this.options       = $.extend({}, Tile.DEFAULTS, options);
			this.transitioning = null;
			this.$element      = $(element);

			if (this.options.parent) {
				this.$parent = this.getParent();
			};

			if (this.options.toggle) {
				this.toggle();
			};
		};

		if (!$.fn.collapse) {
			throw new Error('Menu requires Bootstrap collapse.js');
		};

		Tile.DEFAULTS = {
			keyboard: true,
			toggle: true
		};
		Tile.TRANSITION_DURATION = 150;

		Tile.prototype = $.extend({}, $.fn.collapse.Constructor.prototype);

		Tile.prototype.escape = function () {
			if (this.$element.hasClass('in') && this.options.keyboard) {
				$(document).on('keydown.dismiss.bs.tile', $.proxy(function (e) {
					e.which == 27 && this.hide();
				}, this));
			} else if (!this.$element.hasClass('in')) {
				this.$element.off('keydown.dismiss.bs.tile');
			};
		};

		Tile.prototype.hide = function () {
			if (this.transitioning || !this.$element.hasClass('in')) {
				return;
			};

			var startEvent = $.Event('hide.bs.tile');

			this.$element.trigger(startEvent);

			if (startEvent.isDefaultPrevented()) {
				return;
			};

			var dimension = this.dimension();

			this.$element[dimension](this.$element[dimension]())[0].offsetHeight;

			this.$element.addClass('collapsing').removeClass('collapse in');

			this.$element.closest('.tile-collapse').removeClass('active');

			this.transitioning = 1

			var complete = function () {
				this.transitioning = 0;
				this.$element.removeClass('collapsing').addClass('collapse').trigger('hidden.bs.tile');
				this.escape();
			};

			if (!$.support.transition) {
				return complete.call(this);
			};

			this.$element[dimension](0).one('bsTransitionEnd', $.proxy(complete, this)).emulateTransitionEnd(Tile.TRANSITION_DURATION);
		};

		Tile.prototype.show = function () {
			if (this.transitioning || this.$element.hasClass('in')) {
				return;
			};

			var actives = this.$parent && this.$parent.find('.tile-collapse').children('.in, .collapsing');
			var activesData;

			if (actives && actives.length) {
				activesData = actives.data('bs.tile');
				if (activesData && activesData.transitioning) {
					return;
				};
			};

			var startEvent = $.Event('show.bs.tile');

			this.$element.trigger(startEvent);

			if (startEvent.isDefaultPrevented()) {
				return;
			};

			if (actives && actives.length) {
				Plugin.call(actives, 'hide');
				activesData || actives.data('bs.tile', null);
			};

			var dimension = this.dimension();

			this.$element.removeClass('collapse').addClass('collapsing')[dimension](0);

			this.$element.closest('.tile-collapse').addClass('active');

			this.transitioning = 1;

			var complete = function () {
				this.$element.removeClass('collapsing').addClass('collapse in')[dimension]('');
				this.transitioning = 0;
				this.$element.trigger('shown.bs.tile');
				this.escape();
			};

			if (!$.support.transition) {
				return complete.call(this);
			};

			var scrollSize = $.camelCase(['scroll', dimension].join('-'));

			this.$element.one('bsTransitionEnd', $.proxy(complete, this)).emulateTransitionEnd(Tile.TRANSITION_DURATION)[dimension](this.$element[0][scrollSize]);
		};

		function getTargetFromTrigger($trigger) {
			var href;
			var target = $trigger.attr('data-target') || (href = $trigger.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '');

			return $(target);
		};

		function Plugin(option) {
			return this.each(function () {
				var $this   = $(this);
				var data    = $this.data('bs.tile');
				var options = $.extend({}, Tile.DEFAULTS, $this.data(), typeof option == 'object' && option);

				if (!data && options.toggle && /show|hide/.test(option)) {
					options.toggle = false;
				};

				if (!data) {
					$this.data('bs.tile', (data = new Tile(this, options)));
				};

				if (typeof option == 'string') {
					data[option]();
				};
			})
		};

		var old = $.fn.tile;

		$.fn.tile             = Plugin;
		$.fn.tile.Constructor = Tile;

		$.fn.tile.noConflict = function () {
			$.fn.tile = old;
			return this;
		};

		$(document).on('click.bs.tile.data-api', '[data-toggle="tile"]', function (e) {
			var $this = $(this);

			if (!$(e.target).is('[data-ignore="tile"], [data-ignore="tile"] *')) {
				if (!$this.attr('data-target')) {
					e.preventDefault();
				};

				var $target = getTargetFromTrigger($this);
				var data    = $target.data('bs.tile');
				var option  = data ? 'toggle' : $this.data();

				Plugin.call($target, option);
			};
		});
	}(jQuery));
