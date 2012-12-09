/**
*	jQuery
**/
$(document).ready(function(){
	$("a[rel='img']").colorbox({maxWidth: "98%", maxHeight: "98%"}); //, rel: true

	$(".corner").corner("round 4px");
	$(".button").corner("round 3px");
});

/**
*	Java Script
**/
function open_window(link,w,h)
{
	var win = "width="+w+",height="+h+",menubar=no,location=no,resizable=yes,scrollbars=auto";
	newWin = window.open(link,'newWin',win);
	newWin.focus();
}