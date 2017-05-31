/**
*	jQuery
**/
$(document).ready(function(){
	$("a[rel='img']").colorbox({maxWidth: "98%", maxHeight: "98%",
		'onComplete': function(){
			$('#cboxLoadedContent').zoom({'on': 'click'});
		}
	}); //, rel: true

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
		todayHighlight: true
	});

	/* MOVETOP button */
	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) $("a#move_top").fadeIn();
		else                           $("a#move_top").fadeOut(400);
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