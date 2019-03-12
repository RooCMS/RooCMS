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

/**
 * RooCMS ACP UI
 */
(function($) {
	"use strict";
	$(window).on('load', function() {
		//$(".corner").corner("round 4px");

		$("[rel='tooltip']").tooltip();
		$("[rel='popover']").popover();
		$('.toast').toast();
		$(".alert").alert();

		//$(".collapse").collapse({hide: true});

		/* Select */
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
			$('.selectpicker').selectpicker('mobile');
		}
		else {
			$('.selectpicker').selectpicker();
		}

		/* Datepicker */
		/*$(".datepicker").datepicker({
			format: 'dd.mm.yyyy',
			language: 'ru',
			todayHighlight: true,
			orientation: "bottom auto"
		});*/

		/*$(".datepicker-0d").datepicker({
			format: 'dd.mm.yyyy',
			language: 'ru',
			startDate: '0',
			orientation: "bottom auto"
		});*/

		/* Colorpicker */
		//$(".colorpicker").colorpicker();

		/* Tags Input */
		/*$(".tagsinput").tagsinput({
			maxTags: 10,
			trimValue: true
		});*/

		/*$(".addtag").click(function() {
			$("#inputTags").tagsinput('add', $(this).attr("value"));
		});*/

		/*$(".carousel").swipe({
			swipe: function(event, direction, distance, duration, fingerCount, fingerData) {

				if (direction === "left") $(this).carousel('next');
				if (direction === "right") $(this).carousel('prev');
			},
			allowPageScroll:"vertical"
		});*/

		/* Feed eye */
		$(".show-feed-element").hover(function () {
			var l = $(this).find(".fa");
			l.removeClass("text-muted fa-eye-slash").addClass("text-info fa-eye");
		}, function () {
			var l = $(this).find(".fa");
			l.removeClass("text-info fa-eye").addClass("text-muted fa-eye-slash");
		});

		$(".hide-feed-element").hover(function () {
			var l = $(this).find(".fa");
			l.removeClass("text-default fa-eye").addClass("text-danger fa-eye-slash");
		}, function () {
			var l = $(this).find(".fa");
			l.removeClass("text-danger fa-eye-slash").addClass("text-default fa-eye");
		});

		/* Nav tree */
		$(".nav-onoff").on('click', function () {
			$(".nav-off").toggle();

			/*var shstatus = $(this).find("input");
			if(shstatus.is(":checked")) {
				$(".nav-off").show();
			}
			else {
				$(".nav-off").hide();
			}*/
		});

		/* Leight */
		/*$('[maxleight]').keyup(function(){
			var ml = $(this).attr('maxleight');
			var fl = $(this).val().length;
			var c = (fl > ml) ? 'red t10' : 'grey t10';
			if(fl > ml) {
				$('#calcbd').text('Введено: ' + $(this).val().length + ' Лишний текст будет обрезан');
			}
			else {
				$('#calcbd').text('Введено: ' + $(this).val().length);
			}
			$('#calcbd').attr('class', c);
		});*/

		/* Alert */
		/*setTimeout(function() {
			var ah = $(".alert-info").height();
			var mm = ah + 100;
			$(".alert-info").animate({'margin-top': '-='+mm+'px'}, 1200, function() {
				$(this).hide();
			});
		}, 3700);*/
	});
})(jQuery);
