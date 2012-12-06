<?php

class tpl_items_module_google_plusone {

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
global $config;
$HTML = <<<HTML
<g:plusone{$config->google_plusone_size}{$config->google_plusone_count}></g:plusone>
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
<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
  {lang: 'ru'}
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