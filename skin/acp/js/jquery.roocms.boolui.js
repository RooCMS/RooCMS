/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2020 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

(function($) {
	"use strict";
	var defaults = {
		icon_on: 'fa-check-square',
		icon_off: 'fa-square'
	};

	var methods = {
		init : function(options) {

			// settings
			var settings = $.extend({}, defaults, options);
			var findels  = $('.fas, .far, .fal');

			return $(this).each(function() {

				$(this).children("label").each(function() {

					var labelstatus = ($(this).hasClass("active")) ? true : false ;
					var buttonicon = $(this).find(findels);

					if(labelstatus) {
						buttonicon.removeClass(settings.icon_off).addClass(settings.icon_on);
					}
					else {
						buttonicon.removeClass(settings.icon_on).addClass(settings.icon_off);
					}
				}).on('click', function() {

					$(this).parent().each(function() {
						var buttonicon = $(this).find(findels);
						var inputtype = $(this).find("input");
						if(inputtype.is(":radio")) {
							buttonicon.removeClass(settings.icon_on).addClass(settings.icon_off);
						}
					});

					var tbuttonicon = $(this).find(findels);
					var tinputtype = $(this).find("input");

					if(tinputtype.is(":radio")) {
						tbuttonicon.removeClass(settings.icon_off).addClass(settings.icon_on);

					}

					if(tinputtype.is(":checkbox")) {
						if(!$(this).hasClass("active")) {
							tbuttonicon.removeClass(settings.icon_off).addClass(settings.icon_on);
						}
						else {
							tbuttonicon.removeClass(settings.icon_on).addClass(settings.icon_off);
						}
					}

				});
			});
		}
	};

	$.fn.roocmsboolui = function(method) {
		// logic
		if (methods[method]) {
			return methods[method].apply( this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || ! method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Метод с именем ' +  method + ' не существует для jQuery.booluiroocms');
		}
	};

})(jQuery);


/**
 * Lets begin
 */
(function($) {
	$(window).on('load', function() {
		$(".roocms-boolui").roocmsboolui();
	});
})(jQuery);
