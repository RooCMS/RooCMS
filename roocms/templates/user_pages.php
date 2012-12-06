<?php

class tpl_items_user_pages {

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
$HTML = <<<HTML
	{html:content}
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

//****************************************************
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
// 	{html:title}
//	Заголовок страницы
function page_title($title) {
$HTML = <<<HTML
	<h1>{$title}</h1>
HTML;
return $HTML;
}


//***************************
// 	{html:content}
//	Отрисовка простой текстовой страницы
function page_type_text($content) {
$HTML = <<<HTML
	{html:title}
	<div id="content">
		{$content}
	</div>
HTML;
return $HTML;
}


//***************************
// 	{html:content}
//	Отрисовка страницы с типом bbcode
function page_type_bbcode($content) {
$HTML = <<<HTML
	{html:title}
	<div id="content">
		{$content}
	</div>
HTML;
return $HTML;
}


//***************************
// 	{html:content}
//	Отрисовка страницы с типом html
function page_type_html($content) {
$HTML = <<<HTML
	{html:title}
	<div id="content">
		{$content}
	</div>
HTML;
return $HTML;
}


//***************************
// 	{html:content}
//	Отрисовка страницы с типом php
function page_type_php($content) {
$HTML = <<<HTML
	{html:title}
	<div id="content">
		{$content}
	</div>
HTML;
return $HTML;
}



// end class
}

?>