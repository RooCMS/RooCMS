/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
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
(function($) {
	"use strict";
	$(function(){
		//$(".corner").corner("round 4px");

		$("[rel='tooltip']").tooltip();
		$("[rel='popover']").popover();
		$(".toast").toast('show');
		$(".alert").alert();

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
			endDate: "18/01/2038",
			orientation: "bottom auto"
		});

		/* Swiper */
		/*$(".carousel").swipe({
			swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
				if (direction === "left") $(this).carousel('next');
				if (direction === "right") $(this).carousel('prev');
			},
			allowPageScroll:"vertical"
		});*/

		/* Top navigation */
		$(".navigation-full, .navigation-submenu").hover(function() {
			$(".navigation-submenu").show();
		}, function() {
			$(".navigation-submenu").hide();
		});

		$(".navigation-full-xs").click(function() {
			$(".navigation-submenu").slideToggle();
		});

		/* MOVETOP button */
		$(window).scroll(function () {
			if ($(this).scrollTop() > 300) {
				$("a#move_top").fadeIn();
			}
			else {
				$("a#move_top").fadeOut(400);
			}
		});
		$("a#move_top").on('click', function () {
			$("html, body").animate({scrollTop: 0}, '500', 'swing');
			return false;
		});

		$("#ExpressMailing").on('focus', function() {
			$("#captchaMailing").collapse('show');
		});

		/* Captcha */
		$(".refresh-CaptchaCode").on('click', function () {
			var d = new Date();
			$(".CaptchaCode").attr("src", "/captcha.php?"+d.getTime());
			$(".zoom-CaptchaCode").attr("href", "/captcha.php?"+d.getTime());
			return false;
		});

		$(".recycle-CaptchaCode").on('click', function () {
			var d = new Date();
			$(".CaptchaCode").attr("src", "/captcha.php?I_have_bad_sight=true&"+d.getTime());
			$(".zoom-CaptchaCode").attr("href", "/captcha.php?"+d.getTime());
			return false;
		});

		$(".zoom-CaptchaCode").on('click', function () {
			var d = new Date();
			$(this).attr("href", "/captcha.php?"+d.getTime());
		});

		/* From validation */
		window.addEventListener('load', function() {
			// Fetch all the forms we want to apply custom Bootstrap validation styles to
			var forms = document.getElementsByClassName('needs-validation');
			// Loop over them and prevent submission
			var validation = Array.prototype.filter.call(forms, function(form) {
				form.addEventListener('submit', function(event) {
					if (form.checkValidity() === false) {
						event.preventDefault();
						event.stopPropagation();
					}
					form.classList.add('was-validated');
				}, false);
			});
		}, false);
	});
})(jQuery);

/**
* Java Script
**/
/*function open_window(link,w,h)
{
	var win = "width="+w+",height="+h+",menubar=no,location=no,resizable=yes,scrollbars=auto";
	newWin = window.open(link,'newWin',win);
	newWin.focus();
}*/