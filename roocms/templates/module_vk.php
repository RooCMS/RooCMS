<?php

class tpl_items_module_vk {

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
$HTML = <<<HTML

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
global $config;
$JS = <<<JS
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?22"></script>
<script type="text/javascript">
  VK.init({
	apiId: {$config->vk_apiid}, onlyWidgets: true
	});
</script>
JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################




// end class
}
?>