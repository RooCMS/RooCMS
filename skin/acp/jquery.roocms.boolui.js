/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2019 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

(function($) {

	var defaults = {
		icon_on: 'fa-check-square-o',
		icon_off: 'fa-square-o'
	};

	var methods = {
		init : function(options) {

			// settings
			var settings = $.extend({}, defaults, options);

			return this.each(function() {
				$(this).children("label").each(function() {

					var labelstatus = ($(this).hasClass("active")) ? true : false ;
					var buttonicon = $(this).find(".fa");

					if(labelstatus) {
						buttonicon.removeClass(settings.icon_off).addClass(settings.icon_on);
					}
					else {
						buttonicon.removeClass(settings.icon_on).addClass(settings.icon_off);
					}
				}).click(function(){
					$(this).parent().each(function() {
						var buttonicon = $(this).find(".fa");
						var inputtype = $(this).find("input");
						if(inputtype.is(":radio")) {
							buttonicon.removeClass(settings.icon_on).addClass(settings.icon_off);
						}
					});

					var tbuttonicon = $(this).find(".fa");
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

})( jQuery );


/**
 * Lets begin
 */
$(document).ready(function() {
	$(".roocms-boolui").roocmsboolui();
});