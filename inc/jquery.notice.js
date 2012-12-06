$(document).ready(function() {
	
	$("#info").dialog({
		title: 'Информация:',
		width: '30%',
		hide: 'fade',
		buttons: { "Ok": function() { $(this).dialog("close"); } },
		modal: true
	});
	
	$("#error").dialog({
		title: 'ВНИМАНИЕ!',
		width: '30%',
		show: 'highlight',
		hide: 'fade', 
		dialogClass: 'ui-state-error ui-corner-all',
		modal: true
	});
});
