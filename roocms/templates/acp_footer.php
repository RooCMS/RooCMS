<?php

class tpl_items_acp_footer {

//#########################################################
//#		Основной шаблон
//#########################################################
function tpl_page() {
$ver = VERSION;
$HTML = <<<HTML

	<div id="copyright">
	<br /><a href="http://www.roocms.com/" target="_blank" title="RooCMS">RooCMS</a>  &copy; 2010-2011 
	<br />Версия {$ver}</div>
	</div>
	
	</div>
	</center>
	
	</body>
	</html>
HTML;
return $HTML;
}

//*****************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS

CSS;
return $CSS;
}

//*****************************************************
// JS
function tpl_js() {
$JS = <<<JS

JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################

}

?>