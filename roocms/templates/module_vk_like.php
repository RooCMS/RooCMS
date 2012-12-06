<?php

class tpl_items_module_vk_like {

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
global $config;
$HTML = <<<HTML
<div id="vk_like" style="display: inline;"></div>
<script type="text/javascript">
	/*VK.Widgets.Like("vk_like", {type: "button"});*/
	VK.Widgets.Like("vk_like", {
			type: "{$config->vk_like_type}",
			verb: {$config->vk_like_verb},
			width: "200"
		}
	);
</script>
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

JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################


// end class
}
?>