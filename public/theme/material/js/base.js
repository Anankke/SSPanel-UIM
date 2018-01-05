/*! 
 * modernizr 3.3.0 (Custom Build) | MIT
 * http://modernizr.com/download/?-touchevents-setclasses
 */

!function(e,n,t){function o(e,n){return typeof e===n}function s(){var e,n,t,s,a,i,r;for(var l in c)if(c.hasOwnProperty(l)){if(e=[],n=c[l],n.name&&(e.push(n.name.toLowerCase()),n.options&&n.options.aliases&&n.options.aliases.length))for(t=0;t<n.options.aliases.length;t++)e.push(n.options.aliases[t].toLowerCase());for(s=o(n.fn,"function")?n.fn():n.fn,a=0;a<e.length;a++)i=e[a],r=i.split("."),1===r.length?Modernizr[r[0]]=s:(!Modernizr[r[0]]||Modernizr[r[0]]instanceof Boolean||(Modernizr[r[0]]=new Boolean(Modernizr[r[0]])),Modernizr[r[0]][r[1]]=s),f.push((s?"":"no-")+r.join("-"))}}function a(e){var n=u.className,t=Modernizr._config.classPrefix||"";if(p&&(n=n.baseVal),Modernizr._config.enableJSClass){var o=new RegExp("(^|\\s)"+t+"no-js(\\s|$)");n=n.replace(o,"$1"+t+"js$2")}Modernizr._config.enableClasses&&(n+=" "+t+e.join(" "+t),p?u.className.baseVal=n:u.className=n)}function i(){return"function"!=typeof n.createElement?n.createElement(arguments[0]):p?n.createElementNS.call(n,"http://www.w3.org/2000/svg",arguments[0]):n.createElement.apply(n,arguments)}function r(){var e=n.body;return e||(e=i(p?"svg":"body"),e.fake=!0),e}function l(e,t,o,s){var a,l,f,c,d="modernizr",p=i("div"),h=r();if(parseInt(o,10))for(;o--;)f=i("div"),f.id=s?s[o]:d+(o+1),p.appendChild(f);return a=i("style"),a.type="text/css",a.id="s"+d,(h.fake?h:p).appendChild(a),h.appendChild(p),a.styleSheet?a.styleSheet.cssText=e:a.appendChild(n.createTextNode(e)),p.id=d,h.fake&&(h.style.background="",h.style.overflow="hidden",c=u.style.overflow,u.style.overflow="hidden",u.appendChild(h)),l=t(p,e),h.fake?(h.parentNode.removeChild(h),u.style.overflow=c,u.offsetHeight):p.parentNode.removeChild(p),!!l}var f=[],c=[],d={_version:"3.3.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,n){var t=this;setTimeout(function(){n(t[e])},0)},addTest:function(e,n,t){c.push({name:e,fn:n,options:t})},addAsyncTest:function(e){c.push({name:null,fn:e})}},Modernizr=function(){};Modernizr.prototype=d,Modernizr=new Modernizr;var u=n.documentElement,p="svg"===u.nodeName.toLowerCase(),h=d._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):[];d._prefixes=h;var m=d.testStyles=l;Modernizr.addTest("touchevents",function(){var t;if("ontouchstart"in e||e.DocumentTouch&&n instanceof DocumentTouch)t=!0;else{var o=["@media (",h.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");m(o,function(e){t=9===e.offsetTop})}return t}),s(),a(f),delete d.addTest,delete d.addAsyncTest;for(var v=0;v<Modernizr._q.length;v++)Modernizr._q[v]();e.Modernizr=Modernizr}(window,document);

/*!
 * bootstrap v3.3.5 (http://getbootstrap.com)
 * affix, collapse, dropdown, modal, tab, transition
 */

if("undefined"==typeof jQuery)throw new Error("Bootstrap's JavaScript requires jQuery");+function(t){"use strict";var e=t.fn.jquery.split(" ")[0].split(".");if(e[0]<2&&e[1]<9||1==e[0]&&9==e[1]&&e[2]<1)throw new Error("Bootstrap's JavaScript requires jQuery version 1.9.1 or higher")}(jQuery),+function(t){"use strict";function e(e){var i=e.attr("data-target");i||(i=e.attr("href"),i=i&&/#[A-Za-z]/.test(i)&&i.replace(/.*(?=#[^\s]*$)/,""));var n=i&&t(i);return n&&n.length?n:e.parent()}function i(i){i&&3===i.which||(t(o).remove(),t(s).each(function(){var n=t(this),o=e(n),s={relatedTarget:this};o.hasClass("open")&&(i&&"click"==i.type&&/input|textarea/i.test(i.target.tagName)&&t.contains(o[0],i.target)||(o.trigger(i=t.Event("hide.bs.dropdown",s)),i.isDefaultPrevented()||(n.attr("aria-expanded","false"),o.removeClass("open").trigger("hidden.bs.dropdown",s))))}))}function n(e){return this.each(function(){var i=t(this),n=i.data("bs.dropdown");n||i.data("bs.dropdown",n=new a(this)),"string"==typeof e&&n[e].call(i)})}var o=".dropdown-backdrop",s='[data-toggle="dropdown"]',a=function(e){t(e).on("click.bs.dropdown",this.toggle)};a.VERSION="3.3.5",a.prototype.toggle=function(n){var o=t(this);if(!o.is(".disabled, :disabled")){var s=e(o),a=s.hasClass("open");if(i(),!a){"ontouchstart"in document.documentElement&&!s.closest(".navbar-nav").length&&t(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(t(this)).on("click",i);var r={relatedTarget:this};if(s.trigger(n=t.Event("show.bs.dropdown",r)),n.isDefaultPrevented())return;o.trigger("focus").attr("aria-expanded","true"),s.toggleClass("open").trigger("shown.bs.dropdown",r)}return!1}},a.prototype.keydown=function(i){if(/(38|40|27|32)/.test(i.which)&&!/input|textarea/i.test(i.target.tagName)){var n=t(this);if(i.preventDefault(),i.stopPropagation(),!n.is(".disabled, :disabled")){var o=e(n),a=o.hasClass("open");if(!a&&27!=i.which||a&&27==i.which)return 27==i.which&&o.find(s).trigger("focus"),n.trigger("click");var r=" li:not(.disabled):visible a",d=o.find(".dropdown-menu"+r);if(d.length){var l=d.index(i.target);38==i.which&&l>0&&l--,40==i.which&&l<d.length-1&&l++,~l||(l=0),d.eq(l).trigger("focus")}}}};var r=t.fn.dropdown;t.fn.dropdown=n,t.fn.dropdown.Constructor=a,t.fn.dropdown.noConflict=function(){return t.fn.dropdown=r,this},t(document).on("click.bs.dropdown.data-api",i).on("click.bs.dropdown.data-api",".dropdown form",function(t){t.stopPropagation()}).on("click.bs.dropdown.data-api",s,a.prototype.toggle).on("keydown.bs.dropdown.data-api",s,a.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",a.prototype.keydown)}(jQuery),+function(t){"use strict";function e(e,n){return this.each(function(){var o=t(this),s=o.data("bs.modal"),a=t.extend({},i.DEFAULTS,o.data(),"object"==typeof e&&e);s||o.data("bs.modal",s=new i(this,a)),"string"==typeof e?s[e](n):a.show&&s.show(n)})}var i=function(e,i){this.options=i,this.$body=t(document.body),this.$element=t(e),this.$dialog=this.$element.find(".modal-dialog"),this.$backdrop=null,this.isShown=null,this.originalBodyPad=null,this.scrollbarWidth=0,this.ignoreBackdropClick=!1,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,t.proxy(function(){this.$element.trigger("loaded.bs.modal")},this))};i.VERSION="3.3.5",i.TRANSITION_DURATION=300,i.BACKDROP_TRANSITION_DURATION=150,i.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},i.prototype.toggle=function(t){return this.isShown?this.hide():this.show(t)},i.prototype.show=function(e){var n=this,o=t.Event("show.bs.modal",{relatedTarget:e});this.$element.trigger(o),this.isShown||o.isDefaultPrevented()||(this.isShown=!0,this.checkScrollbar(),this.setScrollbar(),this.$body.addClass("modal-open"),this.escape(),this.resize(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',t.proxy(this.hide,this)),this.$dialog.on("mousedown.dismiss.bs.modal",function(){n.$element.one("mouseup.dismiss.bs.modal",function(e){t(e.target).is(n.$element)&&(n.ignoreBackdropClick=!0)})}),this.backdrop(function(){var o=t.support.transition&&n.$element.hasClass("fade");n.$element.parent().length||n.$element.appendTo(n.$body),n.$element.show().scrollTop(0),n.adjustDialog(),o&&n.$element[0].offsetWidth,n.$element.addClass("in"),n.enforceFocus();var s=t.Event("shown.bs.modal",{relatedTarget:e});o?n.$dialog.one("bsTransitionEnd",function(){n.$element.trigger("focus").trigger(s)}).emulateTransitionEnd(i.TRANSITION_DURATION):n.$element.trigger("focus").trigger(s)}))},i.prototype.hide=function(e){e&&e.preventDefault(),e=t.Event("hide.bs.modal"),this.$element.trigger(e),this.isShown&&!e.isDefaultPrevented()&&(this.isShown=!1,this.escape(),this.resize(),t(document).off("focusin.bs.modal"),this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"),this.$dialog.off("mousedown.dismiss.bs.modal"),t.support.transition&&this.$element.hasClass("fade")?this.$element.one("bsTransitionEnd",t.proxy(this.hideModal,this)).emulateTransitionEnd(i.TRANSITION_DURATION):this.hideModal())},i.prototype.enforceFocus=function(){t(document).off("focusin.bs.modal").on("focusin.bs.modal",t.proxy(function(t){this.$element[0]===t.target||this.$element.has(t.target).length||this.$element.trigger("focus")},this))},i.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keydown.dismiss.bs.modal",t.proxy(function(t){27==t.which&&this.hide()},this)):this.isShown||this.$element.off("keydown.dismiss.bs.modal")},i.prototype.resize=function(){this.isShown?t(window).on("resize.bs.modal",t.proxy(this.handleUpdate,this)):t(window).off("resize.bs.modal")},i.prototype.hideModal=function(){var t=this;this.$element.hide(),this.backdrop(function(){t.$body.removeClass("modal-open"),t.resetAdjustments(),t.resetScrollbar(),t.$element.trigger("hidden.bs.modal")})},i.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},i.prototype.backdrop=function(e){var n=this,o=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var s=t.support.transition&&o;if(this.$backdrop=t(document.createElement("div")).addClass("modal-backdrop "+o).appendTo(this.$body),this.$element.on("click.dismiss.bs.modal",t.proxy(function(t){return this.ignoreBackdropClick?void(this.ignoreBackdropClick=!1):void(t.target===t.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus():this.hide()))},this)),s&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!e)return;s?this.$backdrop.one("bsTransitionEnd",e).emulateTransitionEnd(i.BACKDROP_TRANSITION_DURATION):e()}else if(!this.isShown&&this.$backdrop){this.$backdrop.removeClass("in");var a=function(){n.removeBackdrop(),e&&e()};t.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one("bsTransitionEnd",a).emulateTransitionEnd(i.BACKDROP_TRANSITION_DURATION):a()}else e&&e()},i.prototype.handleUpdate=function(){this.adjustDialog()},i.prototype.adjustDialog=function(){var t=this.$element[0].scrollHeight>document.documentElement.clientHeight;this.$element.css({paddingLeft:!this.bodyIsOverflowing&&t?this.scrollbarWidth:"",paddingRight:this.bodyIsOverflowing&&!t?this.scrollbarWidth:""})},i.prototype.resetAdjustments=function(){this.$element.css({paddingLeft:"",paddingRight:""})},i.prototype.checkScrollbar=function(){var t=window.innerWidth;if(!t){var e=document.documentElement.getBoundingClientRect();t=e.right-Math.abs(e.left)}this.bodyIsOverflowing=document.body.clientWidth<t,this.scrollbarWidth=this.measureScrollbar()},i.prototype.setScrollbar=function(){var t=parseInt(this.$body.css("padding-right")||0,10);this.originalBodyPad=document.body.style.paddingRight||"",this.bodyIsOverflowing&&this.$body.css("padding-right",t+this.scrollbarWidth)},i.prototype.resetScrollbar=function(){this.$body.css("padding-right",this.originalBodyPad)},i.prototype.measureScrollbar=function(){var t=document.createElement("div");t.className="modal-scrollbar-measure",this.$body.append(t);var e=t.offsetWidth-t.clientWidth;return this.$body[0].removeChild(t),e};var n=t.fn.modal;t.fn.modal=e,t.fn.modal.Constructor=i,t.fn.modal.noConflict=function(){return t.fn.modal=n,this},t(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(i){var n=t(this),o=n.attr("href"),s=t(n.attr("data-target")||o&&o.replace(/.*(?=#[^\s]+$)/,"")),a=s.data("bs.modal")?"toggle":t.extend({remote:!/#/.test(o)&&o},s.data(),n.data());n.is("a")&&i.preventDefault(),s.one("show.bs.modal",function(t){t.isDefaultPrevented()||s.one("hidden.bs.modal",function(){n.is(":visible")&&n.trigger("focus")})}),e.call(s,a,this)})}(jQuery),+function(t){"use strict";function e(e){return this.each(function(){var n=t(this),o=n.data("bs.tab");o||n.data("bs.tab",o=new i(this)),"string"==typeof e&&o[e]()})}var i=function(e){this.element=t(e)};i.VERSION="3.3.5",i.TRANSITION_DURATION=150,i.prototype.show=function(){var e=this.element,i=e.closest("ul:not(.dropdown-menu)"),n=e.data("target");if(n||(n=e.attr("href"),n=n&&n.replace(/.*(?=#[^\s]*$)/,"")),!e.parent("li").hasClass("active")){var o=i.find(".active:last a"),s=t.Event("hide.bs.tab",{relatedTarget:e[0]}),a=t.Event("show.bs.tab",{relatedTarget:o[0]});if(o.trigger(s),e.trigger(a),!a.isDefaultPrevented()&&!s.isDefaultPrevented()){var r=t(n);this.activate(e.closest("li"),i),this.activate(r,r.parent(),function(){o.trigger({type:"hidden.bs.tab",relatedTarget:e[0]}),e.trigger({type:"shown.bs.tab",relatedTarget:o[0]})})}}},i.prototype.activate=function(e,n,o){function s(){a.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!1),e.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded",!0),r?(e[0].offsetWidth,e.addClass("in")):e.removeClass("fade"),e.parent(".dropdown-menu").length&&e.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded",!0),o&&o()}var a=n.find("> .active"),r=o&&t.support.transition&&(a.length&&a.hasClass("fade")||!!n.find("> .fade").length);a.length&&r?a.one("bsTransitionEnd",s).emulateTransitionEnd(i.TRANSITION_DURATION):s(),a.removeClass("in")};var n=t.fn.tab;t.fn.tab=e,t.fn.tab.Constructor=i,t.fn.tab.noConflict=function(){return t.fn.tab=n,this};var o=function(i){i.preventDefault(),e.call(t(this),"show")};t(document).on("click.bs.tab.data-api",'[data-toggle="tab"]',o).on("click.bs.tab.data-api",'[data-toggle="pill"]',o)}(jQuery),+function(t){"use strict";function e(e){return this.each(function(){var n=t(this),o=n.data("bs.affix"),s="object"==typeof e&&e;o||n.data("bs.affix",o=new i(this,s)),"string"==typeof e&&o[e]()})}var i=function(e,n){this.options=t.extend({},i.DEFAULTS,n),this.$target=t(this.options.target).on("scroll.bs.affix.data-api",t.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",t.proxy(this.checkPositionWithEventLoop,this)),this.$element=t(e),this.affixed=null,this.unpin=null,this.pinnedOffset=null,this.checkPosition()};i.VERSION="3.3.5",i.RESET="affix affix-top affix-bottom",i.DEFAULTS={offset:0,target:window},i.prototype.getState=function(t,e,i,n){var o=this.$target.scrollTop(),s=this.$element.offset(),a=this.$target.height();if(null!=i&&"top"==this.affixed)return i>o?"top":!1;if("bottom"==this.affixed)return null!=i?o+this.unpin<=s.top?!1:"bottom":t-n>=o+a?!1:"bottom";var r=null==this.affixed,d=r?o:s.top,l=r?a:e;return null!=i&&i>=o?"top":null!=n&&d+l>=t-n?"bottom":!1},i.prototype.getPinnedOffset=function(){if(this.pinnedOffset)return this.pinnedOffset;this.$element.removeClass(i.RESET).addClass("affix");var t=this.$target.scrollTop(),e=this.$element.offset();return this.pinnedOffset=e.top-t},i.prototype.checkPositionWithEventLoop=function(){setTimeout(t.proxy(this.checkPosition,this),1)},i.prototype.checkPosition=function(){if(this.$element.is(":visible")){var e=this.$element.height(),n=this.options.offset,o=n.top,s=n.bottom,a=Math.max(t(document).height(),t(document.body).height());"object"!=typeof n&&(s=o=n),"function"==typeof o&&(o=n.top(this.$element)),"function"==typeof s&&(s=n.bottom(this.$element));var r=this.getState(a,e,o,s);if(this.affixed!=r){null!=this.unpin&&this.$element.css("top","");var d="affix"+(r?"-"+r:""),l=t.Event(d+".bs.affix");if(this.$element.trigger(l),l.isDefaultPrevented())return;this.affixed=r,this.unpin="bottom"==r?this.getPinnedOffset():null,this.$element.removeClass(i.RESET).addClass(d).trigger(d.replace("affix","affixed")+".bs.affix")}"bottom"==r&&this.$element.offset({top:a-e-s})}};var n=t.fn.affix;t.fn.affix=e,t.fn.affix.Constructor=i,t.fn.affix.noConflict=function(){return t.fn.affix=n,this},t(window).on("load",function(){t('[data-spy="affix"]').each(function(){var i=t(this),n=i.data();n.offset=n.offset||{},null!=n.offsetBottom&&(n.offset.bottom=n.offsetBottom),null!=n.offsetTop&&(n.offset.top=n.offsetTop),e.call(i,n)})})}(jQuery),+function(t){"use strict";function e(e){var i,n=e.attr("data-target")||(i=e.attr("href"))&&i.replace(/.*(?=#[^\s]+$)/,"");return t(n)}function i(e){return this.each(function(){var i=t(this),o=i.data("bs.collapse"),s=t.extend({},n.DEFAULTS,i.data(),"object"==typeof e&&e);!o&&s.toggle&&/show|hide/.test(e)&&(s.toggle=!1),o||i.data("bs.collapse",o=new n(this,s)),"string"==typeof e&&o[e]()})}var n=function(e,i){this.$element=t(e),this.options=t.extend({},n.DEFAULTS,i),this.$trigger=t('[data-toggle="collapse"][href="#'+e.id+'"],[data-toggle="collapse"][data-target="#'+e.id+'"]'),this.transitioning=null,this.options.parent?this.$parent=this.getParent():this.addAriaAndCollapsedClass(this.$element,this.$trigger),this.options.toggle&&this.toggle()};n.VERSION="3.3.5",n.TRANSITION_DURATION=350,n.DEFAULTS={toggle:!0},n.prototype.dimension=function(){var t=this.$element.hasClass("width");return t?"width":"height"},n.prototype.show=function(){if(!this.transitioning&&!this.$element.hasClass("in")){var e,o=this.$parent&&this.$parent.children(".panel").children(".in, .collapsing");if(!(o&&o.length&&(e=o.data("bs.collapse"),e&&e.transitioning))){var s=t.Event("show.bs.collapse");if(this.$element.trigger(s),!s.isDefaultPrevented()){o&&o.length&&(i.call(o,"hide"),e||o.data("bs.collapse",null));var a=this.dimension();this.$element.removeClass("collapse").addClass("collapsing")[a](0).attr("aria-expanded",!0),this.$trigger.removeClass("collapsed").attr("aria-expanded",!0),this.transitioning=1;var r=function(){this.$element.removeClass("collapsing").addClass("collapse in")[a](""),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};if(!t.support.transition)return r.call(this);var d=t.camelCase(["scroll",a].join("-"));this.$element.one("bsTransitionEnd",t.proxy(r,this)).emulateTransitionEnd(n.TRANSITION_DURATION)[a](this.$element[0][d])}}}},n.prototype.hide=function(){if(!this.transitioning&&this.$element.hasClass("in")){var e=t.Event("hide.bs.collapse");if(this.$element.trigger(e),!e.isDefaultPrevented()){var i=this.dimension();this.$element[i](this.$element[i]())[0].offsetHeight,this.$element.addClass("collapsing").removeClass("collapse in").attr("aria-expanded",!1),this.$trigger.addClass("collapsed").attr("aria-expanded",!1),this.transitioning=1;var o=function(){this.transitioning=0,this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse")};return t.support.transition?void this.$element[i](0).one("bsTransitionEnd",t.proxy(o,this)).emulateTransitionEnd(n.TRANSITION_DURATION):o.call(this)}}},n.prototype.toggle=function(){this[this.$element.hasClass("in")?"hide":"show"]()},n.prototype.getParent=function(){return t(this.options.parent).find('[data-toggle="collapse"][data-parent="'+this.options.parent+'"]').each(t.proxy(function(i,n){var o=t(n);this.addAriaAndCollapsedClass(e(o),o)},this)).end()},n.prototype.addAriaAndCollapsedClass=function(t,e){var i=t.hasClass("in");t.attr("aria-expanded",i),e.toggleClass("collapsed",!i).attr("aria-expanded",i)};var o=t.fn.collapse;t.fn.collapse=i,t.fn.collapse.Constructor=n,t.fn.collapse.noConflict=function(){return t.fn.collapse=o,this},t(document).on("click.bs.collapse.data-api",'[data-toggle="collapse"]',function(n){var o=t(this);o.attr("data-target")||n.preventDefault();var s=e(o),a=s.data("bs.collapse"),r=a?"toggle":o.data();i.call(s,r)})}(jQuery),+function(t){"use strict";function e(){var t=document.createElement("bootstrap"),e={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var i in e)if(void 0!==t.style[i])return{end:e[i]};return!1}t.fn.emulateTransitionEnd=function(e){var i=!1,n=this;t(this).one("bsTransitionEnd",function(){i=!0});var o=function(){i||t(n).trigger(t.support.transition.end)};return setTimeout(o,e),this},t(function(){t.support.transition=e(),t.support.transition&&(t.event.special.bsTransitionEnd={bindType:t.support.transition.end,delegateType:t.support.transition.end,handle:function(e){return t(e.target).is(this)?e.handleObj.handler.apply(this,arguments):void 0}})})}(jQuery);

// floating label
	(function ($) {
		'use strict';

		$.fn.floatingLabel = function (option) {
			var parent = this.closest('.form-group-label');

			if (parent.length) {
				switch (option) {
					case 'focusin':
						parent.addClass('control-focus');
						break;
					case 'focusout':
						parent.removeClass('control-focus');
						break;
					default: 
						if (this.val()) {
							parent.addClass('control-highlight');
						} else if (this.is('select') && $('option:first-child', this).html().replace(' ', '') !== '') {
							parent.addClass('control-highlight');
						} else {
							parent.removeClass('control-highlight');
						};
				};
			};

			return this;
		};
	}(jQuery));

	$(function () {
		'use strict';

		$('.form-group-label .form-control').each(function () {
			$(this).floatingLabel('change');
		});

		$(document).on('change', '.form-group-label .form-control', function () {
			$(this).floatingLabel('change');
		});

		$(document).on('focusin', '.form-group-label .form-control', function () {
			$(this).floatingLabel('focusin');
		});

		$(document).on('focusout', '.form-group-label .form-control', function () {
			$(this).floatingLabel('focusout');
		});
	});

/*!
 * textarea autosize v0.4.0
 * https://github.com/javierjulio/textarea-autosize
 */

!function(t,e){function i(e){this.element=e,this.$element=t(e),this.init()}var n="textareaAutoSize",h="plugin_"+n,s=function(t){return t.replace(/\s/g,"").length>0};i.prototype={init:function(){var i=(this.$element.outerHeight(),parseInt(this.$element.css("paddingBottom"))+parseInt(this.$element.css("paddingTop")));s(this.element.value)&&this.$element.height(this.element.scrollHeight-i),this.$element.on("input keyup",function(){var n=t(e),h=n.scrollTop();t(this).height(0).height(this.scrollHeight-i),n.scrollTop(h)})}},t.fn[n]=function(e){return this.each(function(){t.data(this,h)||t.data(this,h,new i(this,e))}),this}}(jQuery,window,document);

// textarea autosize default
	$(function () {
		'use strict';

		$('.textarea-autosize').textareaAutoSize();
	});

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

// modale dialog vertical alignment
	$(function () {
		'use strict';

		$(document).on('hidden.bs.modal', '.modal-va-middle', function () {
			$(this).removeClass('modal-va-middle-show');
		});

		$(document).on('show.bs.modal', '.modal-va-middle', function () {
			$(this).addClass('modal-va-middle-show');
		});
	});

/*!
 * pickadate v3.5.6
 * http://amsul.ca
 */
	!function(a){"function"==typeof define&&define.amd?define("picker",["jquery"],a):"object"==typeof exports?module.exports=a(require("jquery")):this.Picker=a(jQuery)}(function(a){function b(f,g,i,m){function n(){return b._.node("div",b._.node("div",b._.node("div",b._.node("div",B.component.nodes(w.open),y.box),y.wrap),y.frame),y.holder,'tabindex="-1"')}function o(){z.data(g,B).addClass(y.input).val(z.data("value")?B.get("select",x.format):f.value),x.editable||z.on("focus."+w.id+" click."+w.id,function(a){a.preventDefault(),B.open()}).on("keydown."+w.id,u),e(f,{haspopup:!0,expanded:!1,readonly:!1,owns:f.id+"_root"})}function p(){e(B.$root[0],"hidden",!0)}function q(){B.$holder.on({keydown:u,"focus.toOpen":t,blur:function(){z.removeClass(y.target)},focusin:function(a){B.$root.removeClass(y.focused),a.stopPropagation()},"mousedown click":function(b){var c=b.target;c!=B.$holder[0]&&(b.stopPropagation(),"mousedown"!=b.type||a(c).is("input, select, textarea, button, option")||(b.preventDefault(),B.$holder[0].focus()))}}).on("click","[data-pick], [data-nav], [data-clear], [data-close]",function(){var b=a(this),c=b.data(),d=b.hasClass(y.navDisabled)||b.hasClass(y.disabled),e=h();e=e&&(e.type||e.href),(d||e&&!a.contains(B.$root[0],e))&&B.$holder[0].focus(),!d&&c.nav?B.set("highlight",B.component.item.highlight,{nav:c.nav}):!d&&"pick"in c?(B.set("select",c.pick),x.closeOnSelect&&B.close(!0)):c.clear?(B.clear(),x.closeOnClear&&B.close(!0)):c.close&&B.close(!0)})}function r(){var b;x.hiddenName===!0?(b=f.name,f.name=""):(b=["string"==typeof x.hiddenPrefix?x.hiddenPrefix:"","string"==typeof x.hiddenSuffix?x.hiddenSuffix:"_submit"],b=b[0]+f.name+b[1]),B._hidden=a('<input type=hidden name="'+b+'"'+(z.data("value")||f.value?' value="'+B.get("select",x.formatSubmit)+'"':"")+">")[0],z.on("change."+w.id,function(){B._hidden.value=f.value?B.get("select",x.formatSubmit):""})}function s(){v&&l?B.$holder.find("."+y.frame).one("transitionend",function(){B.$holder[0].focus()}):B.$holder[0].focus()}function t(a){a.stopPropagation(),z.addClass(y.target),B.$root.addClass(y.focused),B.open()}function u(a){var b=a.keyCode,c=/^(8|46)$/.test(b);return 27==b?(B.close(!0),!1):void((32==b||c||!w.open&&B.component.key[b])&&(a.preventDefault(),a.stopPropagation(),c?B.clear().close():B.open()))}if(!f)return b;var v=!1,w={id:f.id||"P"+Math.abs(~~(Math.random()*new Date))},x=i?a.extend(!0,{},i.defaults,m):m||{},y=a.extend({},b.klasses(),x.klass),z=a(f),A=function(){return this.start()},B=A.prototype={constructor:A,$node:z,start:function(){return w&&w.start?B:(w.methods={},w.start=!0,w.open=!1,w.type=f.type,f.autofocus=f==h(),f.readOnly=!x.editable,f.id=f.id||w.id,"text"!=f.type&&(f.type="text"),B.component=new i(B,x),B.$root=a('<div class="'+y.picker+'" id="'+f.id+'_root" />'),p(),B.$holder=a(n()).appendTo(B.$root),q(),x.formatSubmit&&r(),o(),x.containerHidden?a(x.containerHidden).append(B._hidden):z.after(B._hidden),x.container?a(x.container).append(B.$root):z.after(B.$root),B.on({start:B.component.onStart,render:B.component.onRender,stop:B.component.onStop,open:B.component.onOpen,close:B.component.onClose,set:B.component.onSet}).on({start:x.onStart,render:x.onRender,stop:x.onStop,open:x.onOpen,close:x.onClose,set:x.onSet}),v=c(B.$holder[0]),f.autofocus&&B.open(),B.trigger("start").trigger("render"))},render:function(b){return b?(B.$holder=a(n()),q(),B.$root.html(B.$holder)):B.$root.find("."+y.box).html(B.component.nodes(w.open)),B.trigger("render")},stop:function(){return w.start?(B.close(),B._hidden&&B._hidden.parentNode.removeChild(B._hidden),B.$root.remove(),z.removeClass(y.input).removeData(g),setTimeout(function(){z.off("."+w.id)},0),f.type=w.type,f.readOnly=!1,B.trigger("stop"),w.methods={},w.start=!1,B):B},open:function(c){return w.open?B:(z.addClass(y.active),e(f,"expanded",!0),setTimeout(function(){B.$root.addClass(y.opened),e(B.$root[0],"hidden",!1)},0),c!==!1&&(w.open=!0,v&&k.css("overflow","hidden").css("padding-right","+="+d()),s(),j.on("click."+w.id+" focusin."+w.id,function(a){var b=a.target;b!=f&&b!=document&&3!=a.which&&B.close(b===B.$holder[0])}).on("keydown."+w.id,function(c){var d=c.keyCode,e=B.component.key[d],f=c.target;27==d?B.close(!0):f!=B.$holder[0]||!e&&13!=d?a.contains(B.$root[0],f)&&13==d&&(c.preventDefault(),f.click()):(c.preventDefault(),e?b._.trigger(B.component.key.go,B,[b._.trigger(e)]):B.$root.find("."+y.highlighted).hasClass(y.disabled)||(B.set("select",B.component.item.highlight),x.closeOnSelect&&B.close(!0)))})),B.trigger("open"))},close:function(a){return a&&(x.editable?f.focus():(B.$holder.off("focus.toOpen").focus(),setTimeout(function(){B.$holder.on("focus.toOpen",t)},0))),z.removeClass(y.active),e(f,"expanded",!1),setTimeout(function(){B.$root.removeClass(y.opened+" "+y.focused),e(B.$root[0],"hidden",!0)},0),w.open?(w.open=!1,v&&k.css("overflow","").css("padding-right","-="+d()),j.off("."+w.id),B.trigger("close")):B},clear:function(a){return B.set("clear",null,a)},set:function(b,c,d){var e,f,g=a.isPlainObject(b),h=g?b:{};if(d=g&&a.isPlainObject(c)?c:d||{},b){g||(h[b]=c);for(e in h)f=h[e],e in B.component.item&&(void 0===f&&(f=null),B.component.set(e,f,d)),("select"==e||"clear"==e)&&z.val("clear"==e?"":B.get(e,x.format)).trigger("change");B.render()}return d.muted?B:B.trigger("set",h)},get:function(a,c){if(a=a||"value",null!=w[a])return w[a];if("valueSubmit"==a){if(B._hidden)return B._hidden.value;a="value"}if("value"==a)return f.value;if(a in B.component.item){if("string"==typeof c){var d=B.component.get(a);return d?b._.trigger(B.component.formats.toString,B.component,[c,d]):""}return B.component.get(a)}},on:function(b,c,d){var e,f,g=a.isPlainObject(b),h=g?b:{};if(b){g||(h[b]=c);for(e in h)f=h[e],d&&(e="_"+e),w.methods[e]=w.methods[e]||[],w.methods[e].push(f)}return B},off:function(){var a,b,c=arguments;for(a=0,namesCount=c.length;a<namesCount;a+=1)b=c[a],b in w.methods&&delete w.methods[b];return B},trigger:function(a,c){var d=function(a){var d=w.methods[a];d&&d.map(function(a){b._.trigger(a,B,[c])})};return d("_"+a),d(a),B}};return new A}function c(a){var b,c="position";return a.currentStyle?b=a.currentStyle[c]:window.getComputedStyle&&(b=getComputedStyle(a)[c]),"fixed"==b}function d(){if(k.height()<=i.height())return 0;var b=a('<div style="visibility:hidden;width:100px" />').appendTo("body"),c=b[0].offsetWidth;b.css("overflow","scroll");var d=a('<div style="width:100%" />').appendTo(b),e=d[0].offsetWidth;return b.remove(),c-e}function e(b,c,d){if(a.isPlainObject(c))for(var e in c)f(b,e,c[e]);else f(b,c,d)}function f(a,b,c){a.setAttribute(("role"==b?"":"aria-")+b,c)}function g(b,c){a.isPlainObject(b)||(b={attribute:c}),c="";for(var d in b){var e=("role"==d?"":"aria-")+d,f=b[d];c+=null==f?"":e+'="'+b[d]+'"'}return c}function h(){try{return document.activeElement}catch(a){}}var i=a(window),j=a(document),k=a(document.documentElement),l=null!=document.documentElement.style.transition;return b.klasses=function(a){return a=a||"picker",{picker:a,opened:a+"--opened",focused:a+"--focused",input:a+"__input",active:a+"__input--active",target:a+"__input--target",holder:a+"__holder",frame:a+"__frame",wrap:a+"__wrap",box:a+"__box"}},b._={group:function(a){for(var c,d="",e=b._.trigger(a.min,a);e<=b._.trigger(a.max,a,[e]);e+=a.i)c=b._.trigger(a.item,a,[e]),d+=b._.node(a.node,c[0],c[1],c[2]);return d},node:function(b,c,d,e){return c?(c=a.isArray(c)?c.join(""):c,d=d?' class="'+d+'"':"",e=e?" "+e:"","<"+b+d+e+">"+c+"</"+b+">"):""},lead:function(a){return(10>a?"0":"")+a},trigger:function(a,b,c){return"function"==typeof a?a.apply(b,c||[]):a},digits:function(a){return/\d/.test(a[1])?2:1},isDate:function(a){return{}.toString.call(a).indexOf("Date")>-1&&this.isInteger(a.getDate())},isInteger:function(a){return{}.toString.call(a).indexOf("Number")>-1&&a%1===0},ariaAttr:g},b.extend=function(c,d){a.fn[c]=function(e,f){var g=this.data(c);return"picker"==e?g:g&&"string"==typeof e?b._.trigger(g[e],g,[f]):this.each(function(){var f=a(this);f.data(c)||new b(this,c,d,e)})},a.fn[c].defaults=d.defaults},b});

	!function(a){"function"==typeof define&&define.amd?define(["picker","jquery"],a):"object"==typeof exports?module.exports=a(require("./picker.js"),require("jquery")):a(Picker,jQuery)}(function(a,b){function c(a,b){var c=this,d=a.$node[0],e=d.value,f=a.$node.data("value"),g=f||e,h=f?b.formatSubmit:b.format,i=function(){return d.currentStyle?"rtl"==d.currentStyle.direction:"rtl"==getComputedStyle(a.$root[0]).direction};c.settings=b,c.$node=a.$node,c.queue={min:"measure create",max:"measure create",now:"now create",select:"parse create validate",highlight:"parse navigate create validate",view:"parse create validate viewset",disable:"deactivate",enable:"activate"},c.item={},c.item.clear=null,c.item.disable=(b.disable||[]).slice(0),c.item.enable=-function(a){return a[0]===!0?a.shift():-1}(c.item.disable),c.set("min",b.min).set("max",b.max).set("now"),g?c.set("select",g,{format:h,defaultValue:!0}):c.set("select",null).set("highlight",c.item.now),c.key={40:7,38:-7,39:function(){return i()?-1:1},37:function(){return i()?1:-1},go:function(a){var b=c.item.highlight,d=new Date(b.year,b.month,b.date+a);c.set("highlight",d,{interval:a}),this.render()}},a.on("render",function(){a.$root.find("."+b.klass.selectMonth).on("change",function(){var c=this.value;c&&(a.set("highlight",[a.get("view").year,c,a.get("highlight").date]),a.$root.find("."+b.klass.selectMonth).trigger("focus"))}),a.$root.find("."+b.klass.selectYear).on("change",function(){var c=this.value;c&&(a.set("highlight",[c,a.get("view").month,a.get("highlight").date]),a.$root.find("."+b.klass.selectYear).trigger("focus"))})},1).on("open",function(){var d="";c.disabled(c.get("now"))&&(d=":not(."+b.klass.buttonToday+")"),a.$root.find("button"+d+", select").attr("disabled",!1)},1).on("close",function(){a.$root.find("button, select").attr("disabled",!0)},1)}var d=7,e=6,f=a._;c.prototype.set=function(a,b,c){var d=this,e=d.item;return null===b?("clear"==a&&(a="select"),e[a]=b,d):(e["enable"==a?"disable":"flip"==a?"enable":a]=d.queue[a].split(" ").map(function(e){return b=d[e](a,b,c)}).pop(),"select"==a?d.set("highlight",e.select,c):"highlight"==a?d.set("view",e.highlight,c):a.match(/^(flip|min|max|disable|enable)$/)&&(e.select&&d.disabled(e.select)&&d.set("select",e.select,c),e.highlight&&d.disabled(e.highlight)&&d.set("highlight",e.highlight,c)),d)},c.prototype.get=function(a){return this.item[a]},c.prototype.create=function(a,c,d){var e,g=this;return c=void 0===c?a:c,c==-(1/0)||c==1/0?e=c:b.isPlainObject(c)&&f.isInteger(c.pick)?c=c.obj:b.isArray(c)?(c=new Date(c[0],c[1],c[2]),c=f.isDate(c)?c:g.create().obj):c=f.isInteger(c)||f.isDate(c)?g.normalize(new Date(c),d):g.now(a,c,d),{year:e||c.getFullYear(),month:e||c.getMonth(),date:e||c.getDate(),day:e||c.getDay(),obj:e||c,pick:e||c.getTime()}},c.prototype.createRange=function(a,c){var d=this,e=function(a){return a===!0||b.isArray(a)||f.isDate(a)?d.create(a):a};return f.isInteger(a)||(a=e(a)),f.isInteger(c)||(c=e(c)),f.isInteger(a)&&b.isPlainObject(c)?a=[c.year,c.month,c.date+a]:f.isInteger(c)&&b.isPlainObject(a)&&(c=[a.year,a.month,a.date+c]),{from:e(a),to:e(c)}},c.prototype.withinRange=function(a,b){return a=this.createRange(a.from,a.to),b.pick>=a.from.pick&&b.pick<=a.to.pick},c.prototype.overlapRanges=function(a,b){var c=this;return a=c.createRange(a.from,a.to),b=c.createRange(b.from,b.to),c.withinRange(a,b.from)||c.withinRange(a,b.to)||c.withinRange(b,a.from)||c.withinRange(b,a.to)},c.prototype.now=function(a,b,c){return b=new Date,c&&c.rel&&b.setDate(b.getDate()+c.rel),this.normalize(b,c)},c.prototype.navigate=function(a,c,d){var e,f,g,h,i=b.isArray(c),j=b.isPlainObject(c),k=this.item.view;if(i||j){for(j?(f=c.year,g=c.month,h=c.date):(f=+c[0],g=+c[1],h=+c[2]),d&&d.nav&&k&&k.month!==g&&(f=k.year,g=k.month),e=new Date(f,g+(d&&d.nav?d.nav:0),1),f=e.getFullYear(),g=e.getMonth();new Date(f,g,h).getMonth()!==g;)h-=1;c=[f,g,h]}return c},c.prototype.normalize=function(a){return a.setHours(0,0,0,0),a},c.prototype.measure=function(a,b){var c=this;return b?"string"==typeof b?b=c.parse(a,b):f.isInteger(b)&&(b=c.now(a,b,{rel:b})):b="min"==a?-(1/0):1/0,b},c.prototype.viewset=function(a,b){return this.create([b.year,b.month,1])},c.prototype.validate=function(a,c,d){var e,g,h,i,j=this,k=c,l=d&&d.interval?d.interval:1,m=-1===j.item.enable,n=j.item.min,o=j.item.max,p=m&&j.item.disable.filter(function(a){if(b.isArray(a)){var d=j.create(a).pick;d<c.pick?e=!0:d>c.pick&&(g=!0)}return f.isInteger(a)}).length;if((!d||!d.nav&&!d.defaultValue)&&(!m&&j.disabled(c)||m&&j.disabled(c)&&(p||e||g)||!m&&(c.pick<=n.pick||c.pick>=o.pick)))for(m&&!p&&(!g&&l>0||!e&&0>l)&&(l*=-1);j.disabled(c)&&(Math.abs(l)>1&&(c.month<k.month||c.month>k.month)&&(c=k,l=l>0?1:-1),c.pick<=n.pick?(h=!0,l=1,c=j.create([n.year,n.month,n.date+(c.pick===n.pick?0:-1)])):c.pick>=o.pick&&(i=!0,l=-1,c=j.create([o.year,o.month,o.date+(c.pick===o.pick?0:1)])),!h||!i);)c=j.create([c.year,c.month,c.date+l]);return c},c.prototype.disabled=function(a){var c=this,d=c.item.disable.filter(function(d){return f.isInteger(d)?a.day===(c.settings.firstDay?d:d-1)%7:b.isArray(d)||f.isDate(d)?a.pick===c.create(d).pick:b.isPlainObject(d)?c.withinRange(d,a):void 0});return d=d.length&&!d.filter(function(a){return b.isArray(a)&&"inverted"==a[3]||b.isPlainObject(a)&&a.inverted}).length,-1===c.item.enable?!d:d||a.pick<c.item.min.pick||a.pick>c.item.max.pick},c.prototype.parse=function(a,b,c){var d=this,e={};return b&&"string"==typeof b?(c&&c.format||(c=c||{},c.format=d.settings.format),d.formats.toArray(c.format).map(function(a){var c=d.formats[a],g=c?f.trigger(c,d,[b,e]):a.replace(/^!/,"").length;c&&(e[a]=b.substr(0,g)),b=b.substr(g)}),[e.yyyy||e.yy,+(e.mm||e.m)-1,e.dd||e.d]):b},c.prototype.formats=function(){function a(a,b,c){var d=a.match(/[^\x00-\x7F]+|\w+/)[0];return c.mm||c.m||(c.m=b.indexOf(d)+1),d.length}function b(a){return a.match(/\w+/)[0].length}return{d:function(a,b){return a?f.digits(a):b.date},dd:function(a,b){return a?2:f.lead(b.date)},ddd:function(a,c){return a?b(a):this.settings.weekdaysShort[c.day]},dddd:function(a,c){return a?b(a):this.settings.weekdaysFull[c.day]},m:function(a,b){return a?f.digits(a):b.month+1},mm:function(a,b){return a?2:f.lead(b.month+1)},mmm:function(b,c){var d=this.settings.monthsShort;return b?a(b,d,c):d[c.month]},mmmm:function(b,c){var d=this.settings.monthsFull;return b?a(b,d,c):d[c.month]},yy:function(a,b){return a?2:(""+b.year).slice(2)},yyyy:function(a,b){return a?4:b.year},toArray:function(a){return a.split(/(d{1,4}|m{1,4}|y{4}|yy|!.)/g)},toString:function(a,b){var c=this;return c.formats.toArray(a).map(function(a){return f.trigger(c.formats[a],c,[0,b])||a.replace(/^!/,"")}).join("")}}}(),c.prototype.isDateExact=function(a,c){var d=this;return f.isInteger(a)&&f.isInteger(c)||"boolean"==typeof a&&"boolean"==typeof c?a===c:(f.isDate(a)||b.isArray(a))&&(f.isDate(c)||b.isArray(c))?d.create(a).pick===d.create(c).pick:b.isPlainObject(a)&&b.isPlainObject(c)?d.isDateExact(a.from,c.from)&&d.isDateExact(a.to,c.to):!1},c.prototype.isDateOverlap=function(a,c){var d=this,e=d.settings.firstDay?1:0;return f.isInteger(a)&&(f.isDate(c)||b.isArray(c))?(a=a%7+e,a===d.create(c).day+1):f.isInteger(c)&&(f.isDate(a)||b.isArray(a))?(c=c%7+e,c===d.create(a).day+1):b.isPlainObject(a)&&b.isPlainObject(c)?d.overlapRanges(a,c):!1},c.prototype.flipEnable=function(a){var b=this.item;b.enable=a||(-1==b.enable?1:-1)},c.prototype.deactivate=function(a,c){var d=this,e=d.item.disable.slice(0);return"flip"==c?d.flipEnable():c===!1?(d.flipEnable(1),e=[]):c===!0?(d.flipEnable(-1),e=[]):c.map(function(a){for(var c,g=0;g<e.length;g+=1)if(d.isDateExact(a,e[g])){c=!0;break}c||(f.isInteger(a)||f.isDate(a)||b.isArray(a)||b.isPlainObject(a)&&a.from&&a.to)&&e.push(a)}),e},c.prototype.activate=function(a,c){var d=this,e=d.item.disable,g=e.length;return"flip"==c?d.flipEnable():c===!0?(d.flipEnable(1),e=[]):c===!1?(d.flipEnable(-1),e=[]):c.map(function(a){var c,h,i,j;for(i=0;g>i;i+=1){if(h=e[i],d.isDateExact(h,a)){c=e[i]=null,j=!0;break}if(d.isDateOverlap(h,a)){b.isPlainObject(a)?(a.inverted=!0,c=a):b.isArray(a)?(c=a,c[3]||c.push("inverted")):f.isDate(a)&&(c=[a.getFullYear(),a.getMonth(),a.getDate(),"inverted"]);break}}if(c)for(i=0;g>i;i+=1)if(d.isDateExact(e[i],a)){e[i]=null;break}if(j)for(i=0;g>i;i+=1)if(d.isDateOverlap(e[i],a)){e[i]=null;break}c&&e.push(c)}),e.filter(function(a){return null!=a})},c.prototype.nodes=function(a){var b=this,c=b.settings,g=b.item,h=g.now,i=g.select,j=g.highlight,k=g.view,l=g.disable,m=g.min,n=g.max,o=function(a,b){return c.firstDay&&(a.push(a.shift()),b.push(b.shift())),f.node("thead",f.node("tr",f.group({min:0,max:d-1,i:1,node:"th",item:function(d){return[a[d],c.klass.weekdays,'scope=col title="'+b[d]+'"']}})))}((c.showWeekdaysFull?c.weekdaysFull:c.weekdaysShort).slice(0),c.weekdaysFull.slice(0)),p=function(a){return f.node("div"," ",c.klass["nav"+(a?"Next":"Prev")]+(a&&k.year>=n.year&&k.month>=n.month||!a&&k.year<=m.year&&k.month<=m.month?" "+c.klass.navDisabled:""),"data-nav="+(a||-1)+" "+f.ariaAttr({role:"button",controls:b.$node[0].id+"_table"})+' title="'+(a?c.labelMonthNext:c.labelMonthPrev)+'"')},q=function(){var d=c.showMonthsShort?c.monthsShort:c.monthsFull;return c.selectMonths?f.node("select",f.group({min:0,max:11,i:1,node:"option",item:function(a){return[d[a],0,"value="+a+(k.month==a?" selected":"")+(k.year==m.year&&a<m.month||k.year==n.year&&a>n.month?" disabled":"")]}}),c.klass.selectMonth,(a?"":"disabled")+" "+f.ariaAttr({controls:b.$node[0].id+"_table"})+' title="'+c.labelMonthSelect+'"'):f.node("div",d[k.month],c.klass.month)},r=function(){var d=k.year,e=c.selectYears===!0?5:~~(c.selectYears/2);if(e){var g=m.year,h=n.year,i=d-e,j=d+e;if(g>i&&(j+=g-i,i=g),j>h){var l=i-g,o=j-h;i-=l>o?o:l,j=h}return f.node("select",f.group({min:i,max:j,i:1,node:"option",item:function(a){return[a,0,"value="+a+(d==a?" selected":"")]}}),c.klass.selectYear,(a?"":"disabled")+" "+f.ariaAttr({controls:b.$node[0].id+"_table"})+' title="'+c.labelYearSelect+'"')}return f.node("div",d,c.klass.year)};return f.node("div",(c.selectYears?r()+q():q()+r())+p()+p(1),c.klass.header)+f.node("table",o+f.node("tbody",f.group({min:0,max:e-1,i:1,node:"tr",item:function(a){var e=c.firstDay&&0===b.create([k.year,k.month,1]).day?-7:0;return[f.group({min:d*a-k.day+e+1,max:function(){return this.min+d-1},i:1,node:"td",item:function(a){a=b.create([k.year,k.month,a+(c.firstDay?1:0)]);var d=i&&i.pick==a.pick,e=j&&j.pick==a.pick,g=l&&b.disabled(a)||a.pick<m.pick||a.pick>n.pick,o=f.trigger(b.formats.toString,b,[c.format,a]);return[f.node("div",a.date,function(b){return b.push(k.month==a.month?c.klass.infocus:c.klass.outfocus),h.pick==a.pick&&b.push(c.klass.now),d&&b.push(c.klass.selected),e&&b.push(c.klass.highlighted),g&&b.push(c.klass.disabled),b.join(" ")}([c.klass.day]),"data-pick="+a.pick+" "+f.ariaAttr({role:"gridcell",label:o,selected:d&&b.$node.val()===o?!0:null,activedescendant:e?!0:null,disabled:g?!0:null})),"",f.ariaAttr({role:"presentation"})]}})]}})),c.klass.table,'id="'+b.$node[0].id+'_table" '+f.ariaAttr({role:"grid",controls:b.$node[0].id,readonly:!0}))+f.node("div",f.node("button",c.today,c.klass.buttonToday,"type=button data-pick="+h.pick+(a&&!b.disabled(h)?"":" disabled")+" "+f.ariaAttr({controls:b.$node[0].id}))+f.node("button",c.clear,c.klass.buttonClear,"type=button data-clear=1"+(a?"":" disabled")+" "+f.ariaAttr({controls:b.$node[0].id}))+f.node("button",c.close,c.klass.buttonClose,"type=button data-close=true "+(a?"":" disabled")+" "+f.ariaAttr({controls:b.$node[0].id})),c.klass.footer)},c.defaults=function(a){return{labelMonthNext:"Next month",labelMonthPrev:"Previous month",labelMonthSelect:"Select a month",labelYearSelect:"Select a year",monthsFull:["January","February","March","April","May","June","July","August","September","October","November","December"],monthsShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],weekdaysFull:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],weekdaysShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],today:"Today",clear:"Clear",close:"Close",closeOnSelect:!0,closeOnClear:!0,format:"d mmmm, yyyy",klass:{table:a+"table",header:a+"header",navPrev:a+"nav--prev",navNext:a+"nav--next",navDisabled:a+"nav--disabled",month:a+"month",year:a+"year",selectMonth:a+"select--month",selectYear:a+"select--year",weekdays:a+"weekday",day:a+"day",disabled:a+"day--disabled",selected:a+"day--selected",highlighted:a+"day--highlighted",now:a+"day--today",infocus:a+"day--infocus",outfocus:a+"day--outfocus",footer:a+"footer",buttonClear:a+"button--clear",buttonToday:a+"button--today",buttonClose:a+"button--close"}}}(a.klasses().picker+"__"),a.extend("pickadate",c)});

// material customisation
	(function ($) {
		'use strict';

		var Datepicker = function (element, options) {
			this.options  = options;
			this.$element = $(element);
		};

		Datepicker.DEFAULTS = {
			cancel: 'Cancel',
			closeOnCancel: true,
			closeOnSelect: false,
			container: 'body',
			disable: [],
			firstDay: 0,
			format: 'd/m/yyyy',
			formatSubmit: '',
			max: false,
			min: false,
			monthsFull: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			ok: 'OK',
			onClose: false,
			onOpen: false,
			onRender: false,
			onSet: false,
			onStart: false,
			onStop: false,
			selectMonths: false,
			selectYears: false,
			today: 'Today',
			weekdaysFull: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
			weekdaysShort: ['S', 'M', 'T', 'W', 'T', 'F', 'S']
		};

		Datepicker.prototype.display = function (datepickerApi, datepickerRoot, datepickerValue) {
			$('.picker__date-display', datepickerRoot).remove();

			$('.picker__wrap', datepickerRoot).prepend('<div class="picker__date-display">' +
				'<div class="picker__date-display-top">' +
					'<span class="picker__year-display">' + datepickerApi.get(datepickerValue, 'yyyy') + '</span>' +
				'</div>' +
				'<div class="picker__date-display-bottom">' +
					'<span class="picker__weekday-display">' + datepickerApi.get(datepickerValue, 'dddd') + '</span>' +
					'<span class="picker__day-display">' + datepickerApi.get(datepickerValue, 'd') + '</span>' +
					'<span class="picker__month-display">' + datepickerApi.get(datepickerValue, 'mmm') + '</span>' +
				'</div>' +
			'</div>');
		};

		Datepicker.prototype.show = function () {
			var that = this;

			this.$element.pickadate({
				clear: that.options.cancel,
				close: that.options.ok,
				closeOnClear: that.options.closeOnCancel,
				closeOnSelect: that.options.closeOnSelect,
				container: that.options.container,
				disable: that.options.disable,
				firstDay: that.options.firstDay,
				format: that.options.format,
				formatSubmit: that.options.formatSubmit,
				klass: {
					buttonClear: 'btn btn-flat btn-brand picker__button--clear',
					buttonClose: 'btn btn-flat btn-brand picker__button--close',
					buttonToday: 'btn btn-flat btn-brand picker__button--today',
					navPrev: 'icon icon-lg picker__nav--prev',
					navNext: 'icon icon-lg picker__nav--next',
				},
				max: that.options.max,
				min: that.options.min,
				monthsFull: that.options.monthsFull,
				monthsShort: that.options.monthsShort,
				onClose: that.options.onClose,
				onOpen: that.options.onOpen,
				onRender: that.options.onRender,
				onSet: that.options.onSet,
				onStart: that.options.onStart,
				onStop: that.options.onStop,
				selectMonths: that.options.selectMonths,
				selectYears: that.options.selectYears,
				today: that.options.today,
				weekdaysFull: that.options.weekdaysFull,
				weekdaysShort: that.options.weekdaysShort
			});

			var datepickerApi = this.$element.pickadate('picker'),
			    datepickerNode = datepickerApi.$node,
			    datepickerRoot = datepickerApi.$root;

			datepickerApi.on({
				close: function () {
					$(document.activeElement).blur();
				},
				open: function () {
					if (!$('.picker__date-display', datepickerRoot).length) {
						that.display(datepickerApi, datepickerRoot, 'highlight');
					};
				},
				set: function () {
					if (datepickerApi.get('select') !== null) {
						that.display(datepickerApi, datepickerRoot, 'select');
					};
				}
			});
		};

		function Plugin (option) {
			return this.each(function () {
				var $this   = $(this);
				var data    = $this.data('bs.pickdate');
				var options = $.extend({}, Datepicker.DEFAULTS, $this.data(), typeof option == 'object' && option);

				if (!data) {
					$this.data('bs.pickdate', (data = new Datepicker(this, options)));
				};

				data.show();
			});
		};

		var old = $.fn.pickdate;

		$.fn.pickdate             = Plugin;
		$.fn.pickdate.Constructor = Datepicker;

		$.fn.pickdate.noConflict = function () {
			$.fn.pickdate = old;
			return this;
		};
	}(jQuery));

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

/*!
 * waves v0.7.4
 * http://fian.my.id/Waves
 */

!function(a,b){"use strict";"function"==typeof define&&define.amd?define([],function(){return b.apply(a)}):"object"==typeof exports?module.exports=b.call(a):a.Waves=b.call(a)}("object"==typeof global?global:this,function(){"use strict";function e(a){return null!==a&&a===a.window}function f(a){return e(a)?a:9===a.nodeType&&a.defaultView}function g(a){var b=typeof a;return"function"===b||"object"===b&&!!a}function h(a){return g(a)&&a.nodeType>0}function i(a){var d=c.call(a);return"[object String]"===d?b(a):g(a)&&/^\[object (HTMLCollection|NodeList|Object)\]$/.test(d)&&a.hasOwnProperty("length")?a:h(a)?[a]:[]}function j(a){var b,c,d={top:0,left:0},e=a&&a.ownerDocument;return b=e.documentElement,"undefined"!=typeof a.getBoundingClientRect&&(d=a.getBoundingClientRect()),c=f(e),{top:d.top+c.pageYOffset-b.clientTop,left:d.left+c.pageXOffset-b.clientLeft}}function k(a){var b="";for(var c in a)a.hasOwnProperty(c)&&(b+=c+":"+a[c]+";");return b}function n(a,b,c,d){if(c){d.classList.remove("waves-wrapping"),c.classList.remove("waves-rippling");var e=c.getAttribute("data-x"),f=c.getAttribute("data-y"),g=c.getAttribute("data-scale"),h=c.getAttribute("data-translate"),i=Date.now()-Number(c.getAttribute("data-hold")),j=350-i;0>j&&(j=0),"mousemove"===a.type&&(j=150);var m="mousemove"===a.type?2500:l.duration;setTimeout(function(){var a={top:f+"px",left:e+"px",opacity:"0","-webkit-transition-duration":m+"ms","-moz-transition-duration":m+"ms","-o-transition-duration":m+"ms","transition-duration":m+"ms","-webkit-transform":g+" "+h,"-moz-transform":g+" "+h,"-ms-transform":g+" "+h,"-o-transform":g+" "+h,transform:g+" "+h};c.setAttribute("style",k(a)),setTimeout(function(){try{d.removeChild(c),b.removeChild(d)}catch(a){return!1}},m)},j)}}function p(a){if(o.allowEvent(a)===!1)return null;for(var b=null,c=a.target||a.srcElement;null!==c.parentElement;){if(c.classList.contains("waves-effect")&&!(c instanceof SVGElement)){b=c;break}c=c.parentElement}return b}function q(a){var b=p(a);if(null!==b){if(b.disabled||b.getAttribute("disabled")||b.classList.contains("disabled"))return;if(o.registerEvent(a),"touchstart"===a.type&&l.delay){var c=!1,e=setTimeout(function(){e=null,l.show(a,b)},l.delay),f=function(d){e&&(clearTimeout(e),e=null,l.show(a,b)),c||(c=!0,l.hide(d,b))},g=function(a){e&&(clearTimeout(e),e=null),f(a)};b.addEventListener("touchmove",g,!1),b.addEventListener("touchend",f,!1),b.addEventListener("touchcancel",f,!1)}else l.show(a,b),d&&(b.addEventListener("touchend",l.hide,!1),b.addEventListener("touchcancel",l.hide,!1)),b.addEventListener("mouseup",l.hide,!1),b.addEventListener("mouseleave",l.hide,!1)}}var a=a||{},b=document.querySelectorAll.bind(document),c=Object.prototype.toString,d="ontouchstart"in window,l={duration:750,delay:200,show:function(a,b,c){if(2===a.button)return!1;b=b||this;var d=document.createElement("div");d.className="waves-wrap waves-wrapping",b.appendChild(d);var e=document.createElement("div");e.className="waves-ripple waves-rippling",d.appendChild(e);var f=j(b),g=0,h=0;"touches"in a&&a.touches.length?(g=a.touches[0].pageY-f.top,h=a.touches[0].pageX-f.left):(g=a.pageY-f.top,h=a.pageX-f.left),h=h>=0?h:0,g=g>=0?g:0;var i="scale("+b.clientWidth/100*3+")",m="translate(0,0)";c&&(m="translate("+c.x+"px, "+c.y+"px)"),e.setAttribute("data-hold",Date.now()),e.setAttribute("data-x",h),e.setAttribute("data-y",g),e.setAttribute("data-scale",i),e.setAttribute("data-translate",m);var n={top:g+"px",left:h+"px"};e.classList.add("waves-notransition"),e.setAttribute("style",k(n)),e.classList.remove("waves-notransition"),n["-webkit-transform"]=i+" "+m,n["-moz-transform"]=i+" "+m,n["-ms-transform"]=i+" "+m,n["-o-transform"]=i+" "+m,n.transform=i+" "+m,n.opacity="1";var o="mousemove"===a.type?2500:l.duration;n["-webkit-transition-duration"]=o+"ms",n["-moz-transition-duration"]=o+"ms",n["-o-transition-duration"]=o+"ms",n["transition-duration"]=o+"ms",e.setAttribute("style",k(n))},hide:function(a,b){b=b||this;for(var c=b.getElementsByClassName("waves-wrapping"),d=b.getElementsByClassName("waves-rippling"),e=0,f=d.length;f>e;e++)n(a,b,d[e],c[e])}},m={input:function(a){var b=a.parentNode;if("i"!==b.tagName.toLowerCase()||!b.classList.contains("waves-effect")){var c=document.createElement("i");c.className=a.className+" waves-input-wrapper",a.className="waves-button-input",b.replaceChild(c,a),c.appendChild(a);var d=window.getComputedStyle(a,null),e=d.color,f=d.backgroundColor;c.setAttribute("style","color:"+e+";background:"+f),a.setAttribute("style","background-color:rgba(0,0,0,0);")}},img:function(a){var b=a.parentNode;if("i"!==b.tagName.toLowerCase()||!b.classList.contains("waves-effect")){var c=document.createElement("i");b.replaceChild(c,a),c.appendChild(a)}}},o={touches:0,allowEvent:function(a){var b=!0;return/^(mousedown|mousemove)$/.test(a.type)&&o.touches&&(b=!1),b},registerEvent:function(a){var b=a.type;"touchstart"===b?o.touches+=1:/^(touchend|touchcancel)$/.test(b)&&setTimeout(function(){o.touches&&(o.touches-=1)},500)}};return a.init=function(a){var b=document.body;a=a||{},"duration"in a&&(l.duration=a.duration),"delay"in a&&(l.delay=a.delay),d&&(b.addEventListener("touchstart",q,!1),b.addEventListener("touchcancel",o.registerEvent,!1),b.addEventListener("touchend",o.registerEvent,!1)),b.addEventListener("mousedown",q,!1)},a.attach=function(a,b){a=i(a),"[object Array]"===c.call(b)&&(b=b.join(" ")),b=b?" "+b:"";for(var d,e,f=0,g=a.length;g>f;f++)d=a[f],e=d.tagName.toLowerCase(),-1!==["input","img"].indexOf(e)&&(m[e](d),d=d.parentElement),-1===d.className.indexOf("waves-effect")&&(d.className+=" waves-effect"+b)},a.ripple=function(a,b){a=i(a);var c=a.length;if(b=b||{},b.wait=b.wait||0,b.position=b.position||null,c)for(var d,e,f,g={},h=0,k={type:"mousedown",button:1},m=function(a,b){return function(){l.hide(a,b)}};c>h;h++)if(d=a[h],e=b.position||{x:d.clientWidth/2,y:d.clientHeight/2},f=j(d),g.x=f.left+e.x,g.y=f.top+e.y,k.pageX=g.x,k.pageY=g.y,l.show(k,d),b.wait>=0&&null!==b.wait){var n={type:"mouseup",button:1};setTimeout(m(n,d),b.wait)}},a.calm=function(a){a=i(a);for(var b={type:"mouseup",button:1},c=0,d=a.length;d>c;c++)l.hide(b,a[c])},a.displayEffect=function(b){console.error("Waves.displayEffect() has been deprecated and will be removed in future version. Please use Waves.init() to initialize Waves effect"),a.init(b)},a});

// waves default
	$(function () {
		Waves.attach('.waves-attach');

		Waves.init({
			duration: 600
		});
	});
