<?php

class tpl_items_module_last_news {

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
global $config;
$HTML = <<<HTML
	<h3>{$config->lastnews_title}</h3>
	<br />
	<br />{html:last_news}
HTML;
return $HTML;
}

//*****************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS
.title_anews {font-size: 15px;font-weight: bold;}
.news_brief_image {border: 1px solid #B8AD9A;margin: 5px 5px 5px 0px;}
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
// 	{html:last_news}
//	Анонс новости
function last_news($news) {
$image = "";
if(isset($news['image_news'])) $image .= <<<HTML
	  <td style="width: 80px;" valign="top">
		<a href="news.php?news={$news['id']}"><img src="upload/{$news['image_news']}" width="80" height="80" align="left" alt="{$news['title']}" title="{$news['title']}" class="news_brief_image"></a>
	  </td>
HTML;
$HTML = <<<HTML
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	 <tr>
		{$image}
	  <td valign="top">
		<a href="news.php?news={$news['id']}" class="black" style="padding-bottom: 8px;"><font class="title_anews">{$news['title']}</font></a>
		<br /><font class="rem">{$news['rdate']} - <a href="news.php?category={$news['category_id']}" class="links">{$news['catname']}</a><br /></font>
		
		<div style="margin: 8px 0px 5px 0px;" id="block_text" class="corner">
		<p>{$news['text']}</p>
		</div>
	  </td>
	 </tr>
	</table>
	<div align="right" style="padding: 0px;margin: 0px 0px 10px 0px;">
		<a href="news.php?news={$news['id']}" class="link">подробнее &raquo;</a>
	</div>
HTML;
return $HTML;
}


}
?>