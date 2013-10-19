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

	$(".corner").corner("round 4px");

    $("[rel='tooltip']").tooltip();
    $("[rel='popover']").popover();
    $(".alert").alert();
    $(".affix").affix();

    /* Select */
    $('.selectpicker').selectpicker();

    /* Logotype */
    $("#logo").css({top:'-=62px'});
	$('#logo').mouseover(function(){
		$(this).stop().animate({top: '5px'}, 400);
	}).mouseout(function(){
		$(this).stop().animate({top: '-57px'}, 400);
	});

	/* Datepicker */
	$('.datepicker').datepicker({
	    format: 'dd.mm.yyyy',
	    language: 'ru'
	});

	$('.datepicker-0d').datepicker({
	    format: 'dd.mm.yyyy',
	    language: 'ru',
	    startDate: '0'
	});

	/* Colorpicker */
	$('.colorpicker').colorpicker();

	/* Colorbox */
    //$("a[rel='img']").colorbox({maxWidth: "98%", maxHeight: "98%"});

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