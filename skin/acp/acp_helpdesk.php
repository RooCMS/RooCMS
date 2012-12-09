<?php

class tpl_items_acp_helpdesk {

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
$HTML = <<<HTML

HTML;
return $HTML;
}

//*********************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS
.helpdesk {float: right;cursor: help;}
CSS;
return $CSS;
}

//*********************************************************
// JS
function tpl_js() {
$JS = <<<JS
<script>
	$(document).ready(function() {
		$("a.helpbutton").button({ icons: {secondary: "ui-icon-help"} });
	});
</script>
JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################

//*********************************************************
//	Help
//	{html:helpdesk_*}
function helpdesk($uname, $title, $description) {
$HTML = <<<HTML
	<span id="a_{$uname}" class="helpdesk"><b>[?]</b></span>
	<div id="{$uname}" style="background: #fff;display: none;">
		<!-- <div class="title_text" style="margin-bottom: 8px;">{$title}</div> -->
		{$description}
	</div>
	<script>
		$(document).ready(function(){
			$('#a_{$uname}').click(function() {
				$('#{$uname}').dialog({
					minWidth: 700,
					show: 'clip', hide: 'clip',
					buttons: [{	text: "Закрыть", click: function() { $(this).dialog("close"); }	}],
					title: '{$title}'
				});
			});
		});
	</script>
HTML;
return $HTML;
}

// end class
}
?>