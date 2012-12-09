$(document).ready(function() {
	$(window).load(function () {
		$("#info").dialog({
			title: 'Информация:',
			minWidth: 500,
			show: 'blind',
			hide: 'fade',
			position: ['right','top'],
			buttons: { "Закрыть": function() { $(this).dialog("close"); } },
			modal: false
		});
		setTimeout(function(){$("#info").dialog("close")}, 5000);
		
		$("#error").dialog({
			title: 'ВНИМАНИЕ!',
			minWidth: 700,
			show: 'highlight',
			hide: 'fade',
			buttons: { "Закрыть": function() { $(this).dialog("close"); } },
			dialogClass: 'ui-state-error ui-widget-content ui-state-error ui-widget-header',
			modal: false
		});
	});
});
