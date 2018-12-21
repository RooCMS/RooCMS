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

/**
* RooCMS
**/
$(document).ready(function(){

	//$(".corner").corner("round 4px");

	$("[rel='tooltip']").tooltip();
	$("[rel='popover']").popover();
	$(".alert").alert();
	$(".affix").affix();

	/* Select */
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
		$('.selectpicker').selectpicker('mobile');
	}
	else {
		$('.selectpicker').selectpicker();
	}

	/* Datepicker */
	$(".datepicker").datepicker({
		format: 'dd.mm.yyyy',
		language: 'ru',
		todayHighlight: true,
		orientation: "bottom auto"
	});

	/* Swiper */
	$(".carousel").swipe({
		swipe: function(event, direction, distance, duration, fingerCount, fingerData) {

			if (direction === "left") $(this).carousel('next');
			if (direction === "right") $(this).carousel('prev');
		},
		allowPageScroll:"vertical"
	});

	/* Top navigation */
	$(".navigation-full, .navigation-submenu").hover(function() {
		$(".navigation-submenu").show();
	}, function() {
		$(".navigation-submenu").hide();
	});

	/* MOVETOP button */
	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			$("a#move_top").fadeIn();
		}
		else {
			$("a#move_top").fadeOut(400);
		}
	});
	$("a#move_top").click(function () {
		$("html, body").animate({scrollTop: 0}, '500', 'swing');
		return false;
	});
});


/**
* Java Script
**/
/*function open_window(link,w,h)
{
	var win = "width="+w+",height="+h+",menubar=no,location=no,resizable=yes,scrollbars=auto";
	newWin = window.open(link,'newWin',win);
	newWin.focus();
}*/