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
			this.container = $("#" + options.inputId + '-field')

			var $sel = this.container.find('.svgicons__select')

			var opt = {
				dropdownParent: 'body',
				render: {
					item: function(item, escape) {
						if (item.value.indexOf('svgicons-') == 0) {
							return '<div class="svgicons__si"><div class="svgicons__si__i ' + item.value + '"></div><span>' + escape(item.text) + '</span></div>';
						} else {
							return '<div class="svgicons__si"><div class="svgicons__si__i"><img src="' + (item.value == '_blank_' ? options.blank : options.iconSetUrl + item.value) + '" alt="" /></div><span>' + escape(item.text) + '</span></div>';
						}
					},
					option: function(item, escape) {
						if (item.value.indexOf('svgicons-') == 0) {
							return '<div class="svgicons__i"><div class="svgicons__i__w"><div class="svgicons__i__w__i ' + item.value + '"></div></div><span>' + escape(item.text) + '</span></div>';
						} else {
							return '<div class="svgicons__i"><div class="svgicons__i__w"><div class="svgicons__i__w__i"><img src="' + (item.value == '_blank_' ? options.blank : options.iconSetUrl + item.value) + '" alt="" /></div></div><span>' + escape(item.text) + '</span></div>';
						}
					}
				}
			}

			$sel.selectize(opt)
		}
	});
})(jQuery);
