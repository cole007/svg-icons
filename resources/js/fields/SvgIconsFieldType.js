/**
 * SVG Icons plugin for Craft CMS
 *
 * SvgIconsFieldType JS
 *
 * @author    Fyrebase
 * @copyright Copyright (c) 2016 Fyrebase
 * @link      fyrebase.com
 * @package   SvgIcons
 * @since     0.0.1
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @link      https://github.com/fyrebase/svg-icons
 */

;(function($){
	SvgIconsFieldType = Garnish.Base.extend({
		container: null,
		init: function(options)
		{
			var self = this
			this.options = options;
			this.container = $("#" + this.options.inputId + '-field')
			this.loadSpriteSheets()
			this.poll = setInterval(this.selectize.bind(this), 100);
		},
		loadSpriteSheets: function() {
			var self = this;

			for (var i = 0; i < this.options.spriteSheets.length; i++) {
				var sheet = this.options.spriteSheets[i];
				if ($.inArray(sheet, __svgicons.loading) == -1) {
					__svgicons.loading.push(sheet)
					$.get(sheet, function(data) {
						var div = document.createElement('div')
						div.innerHTML = new XMLSerializer().serializeToString(data.documentElement)
						$svg = $(div).find('> svg')
						$svg.css('display', 'none').prependTo('body')
						__svgicons.loaded.push(sheet)
						__svgicons.loading.splice(__svgicons.loading.indexOf(sheet), 1);
					});
				}
			}

		},
		selectize: function() {
			if (__svgicons.loaded.length == this.options.spriteSheets.length) {
				clearInterval(this.poll)
				this._selectize();
			}
		},
		_selectize: function() {
			var self = this;
			var $sel = this.container.find('.svgicons__select')

			var opt = {
				dropdownParent: 'body',
				render: {
					item: function(item, escape) {
						if (item.value.indexOf('svgicons-css-') > -1) {

							var myRegexp = /(.*\/)(svgicons-.*)/g;
							var match = myRegexp.exec(item.value);
							var c = match[2];

							return '<div class="svgicons__si"><div class="svgicons__si__i ' + c + '"></div><span>' + escape(item.text) + '</span></div>';

						} else if (item.value.indexOf('svgicons-def-') > -1 || item.value.indexOf('svgicons-sym-') > -1) {

							var myRegexp = /(.*\/)(svgicons-.{3}-)(.*)/g;
							var match = myRegexp.exec(item.value);
							var c = match[3];

							return '<div class="svgicons__si"><div class="svgicons__si__i"><svg style="width: 100%; height: 100%;" viewBox="' + $('#' + c)[0].getAttribute('viewBox') +  '"><use xlink:href="#' + c + '" /></svg></div><span>' + escape(item.text) + '</span></div>';

						} else {
							return '<div class="svgicons__si"><div class="svgicons__si__i"><img src="' + (item.value == '_blank_' ? self.options.blank : self.options.iconSetUrl + item.value) + '" alt="" /></div><span>' + escape(item.text) + '</span></div>';
						}
					},
					option: function(item, escape) {
						if (item.value.indexOf('svgicons-css-') > -1) {

							var myRegexp = /(.*\/)(svgicons-.*)/g;
							var match = myRegexp.exec(item.value);
							var c = match[2];

							return '<div class="svgicons__i"><div class="svgicons__i__w"><div class="svgicons__i__w__i ' + c + '"></div></div><span>' + escape(item.text) + '</span></div>';

						} else if (item.value.indexOf('svgicons-def-') > -1 || item.value.indexOf('svgicons-sym-') > -1) {

							var myRegexp = /(.*\/)(svgicons-.{3}-)(.*)/g;
							var match = myRegexp.exec(item.value);
							var c = match[3];

							return '<div class="svgicons__i"><div class="svgicons__i__w"><div class="svgicons__i__w__i"><svg style="width: 100%; height: 100%;" viewBox="' + $('#' + c)[0].getAttribute('viewBox') +  '"><use xlink:href="#' + c + '" /></svg></div></div><span>' + escape(item.text) + '</span></div>';

						} else {
							return '<div class="svgicons__i"><div class="svgicons__i__w"><div class="svgicons__i__w__i"><img src="' + (item.value == '_blank_' ? self.options.blank : self.options.iconSetUrl + item.value) + '" alt="" /></div></div><span>' + escape(item.text) + '</span></div>';
						}
					}
				}
			}

			$sel.selectize(opt)
		}
	});
})(jQuery);
