/**
*	jQuery
**/
$(document).ready(function() {
	$("#tabs").tabs({ 
		collapsible: false/*,
		cookie: {expires: 10}*/
	});

	$("#tabs ul").css("display","block");
	$("a.button").button({
		icons: {primary: "ui-icon-arrowthick-1-w"}
	});
	
	$("input.button").button({
		icons: {primary: "ui-icon-check"}
	});
	$("#addimg").button({icons: {primary: "ui-icon-plusthick"}});
	$("#addfile").button({icons: {primary: "ui-icon-plusthick"}});
	$("#addstep").button({icons: {primary: "ui-icon-plusthick"}});
});