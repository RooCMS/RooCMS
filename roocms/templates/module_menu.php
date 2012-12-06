<?php

class tpl_items_module_menu {

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
$HTML = <<<HTML

  <ul id="menu">
	{html:menu_item}
  </ul>


HTML;
return $HTML;
}

//*****************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS
/* Menu */
ul#menu {margin: 10px 0px 10px 0px; padding: 0px; list-style: none; font-size: 12px; float: right; clear: both; width: 490px;}
ul#menu li{margin: 0px; padding: 0px; height:40px;	float: left; overflow: hidden;}
ul#menu li.now {margin: 0px; padding: 0px; height:40px; float: left; overflow: hidden;	background: #EDE1CC;}
ul#menu a, ul#menu span {font-family: Tahoma; padding: 10px 10px; text-decoration: underline; text-transform: uppercase; color: #373737; float: left; clear: both; height: 20px; line-height: 20px;}
ul#menu a:hover {	color: #F38833; }
ul#menu span {display: none;}
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


//***************************
// 	{html:menu_item}
//	Элемент меню
function menu_item($menu) {
if($menu['link'] == THIS_SCRIPT) $class=" class=\"now\"";
else $class="";
$HTML = <<<HTML
	<li {$class}><a href="{$menu['link']}.php">{$menu['title']}</a></li>
HTML;
return $HTML;
}


}
?>