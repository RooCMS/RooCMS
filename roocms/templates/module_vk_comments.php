<?php

class tpl_items_module_vk_comments {

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
global $config;
$HTML = <<<HTML
<div id="module_vk_comments"></div>
<script type="text/javascript">
	VK.Widgets.Comments("module_vk_comments", {
			limit: {$config->vk_comments_limit}, 
			width: "{$config->vk_comments_width}", 
			attach: "{$config->vk_comments_attached}",
			norealtime: {$config->vk_comments_norealtime},
			autoPublish: {$config->vk_comments_autopublish}
		});
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