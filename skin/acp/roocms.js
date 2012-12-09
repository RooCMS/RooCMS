/**********************************************************
 * This script was developed by alex Roosso.
 * Title: RooCMS jQuery
 * Author:	alex Roosso
 * Copyright: 2010-2012 (c) RooCMS.
 * Web: http://www.roocms.com
 * All rights reserved.
 * ********************************************************
 * 	This program is free software; you can redistribute it and/or modify
 * 	it under the terms of the GNU General Public License as published by
 * 	the Free Software Foundation; either version 2 of the License, or
 * 	(at your option) any later version.
 *
 * 	Данное программное обеспечение является свободным и распространяется
 * 	по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
 * 	При любом использовании данного ПО вы должны соблюдать все условия
 * 	лицензии.
 * ********************************************************
 * 	Build date: 		6:50 13.03.2011
 * 	Last Build: 		2:41 09.10.2012
*/
$(document).ready(function() {

	$("#tabs").tabs({
		collapsible: false/*,
		cookie: {expires: 10}*/
	});

	$(".corner").corner("round 4px");

	$( ".date" ).datepicker({ dateFormat: "dd.mm.yy",
							dayNamesMin: ["Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Вс"],
							monthNamesShort: ["Янв","Фев","Мар","Апр","Май","Июн","Июл","Авг","Сен","Окт","Ноя","Дек"],
							monthNames: ["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"]});

	$(".color").colorpicker().on("mouseover.color", function(event, color){
        $(this).attr("style", "background-color:" + color);
    });



	$("#tabs ul").css("display","block");
	$("a.button").button({
		icons: {primary: "ui-icon-arrowthick-1-w"}
	});

	$('.buttonset').buttonset();

	$("input.button").button({icons: {primary: "ui-icon-check"}});
	$("input.f_submit").button({icons: {primary: "ui-icon-check"}});
	$("#addimg").button({icons: {primary: "ui-icon-plusthick"}});
	$("#addfile").button({icons: {primary: "ui-icon-plusthick"}});
	$("#addstep").button({icons: {primary: "ui-icon-plusthick"}});

	var obg = $('.option').css('background-color');
	$('.option').mouseover(function(){
		$(this).stop().animate({backgroundColor: '#F7F2E5'}, 400);
	}).mouseout(function(){
		$(this).stop().animate({backgroundColor: obg}, 400);
	});

	/* Placeholder for IE */
	if($.browser.msie) {
		$("input[type='text']").each(function() {
			var tp = $(this).attr("placeholder");
			if(tp != undefined) $(this).attr('value',tp).css('color','#ccc');
		}).focusin(function() {
			var val = $(this).attr('placeholder');
			if($(this).val() == val) {
				$(this).attr('value','').css('color','#303030');
			}
		}).focusout(function() {
			var val = $(this).attr('placeholder');
			if($(this).val() == "") {
				$(this).attr('value', val).css('color','#ccc');
			}
		});

		/* Protected send form */
		$("form").submit(function() {
			$(this).children("input[type='text']").each(function() {
				var val = $(this).attr('placeholder');
				if($(this).val() == val) {
					$(this).attr('value','').css('color','#303030');
				}
			})
		});
	}
});