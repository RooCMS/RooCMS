<?php

class tpl_items_user_portfolio {

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
$HTML = <<<HTML
	<h1>Портфолио</h1>
	{html:head}
	<div id="content" style="padding: 0px 0px 15px 0px; width: 1000px;">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td valing="top" style="vertical-align: top;">
			<div id="portfolio_category">
				{html:category}
			</div>
			<div style="text-align: center;">
				<div style="padding: 15px;">{html:tag}</div>
			</div>
		</td>
		<td valign="top" style="vertical-align: top; width: 100%;">
			{html:part_title}
			<div id="portfolio_content">
				{html:content}
			</div>
			{html:navpage}
			<div class="rem" style="margin: 3px;">
			Все работы указанные в разделе являются лишь демонстрацей работы самого раздела и не имееют в себе ничего общего с реальными работами или их авторами.
			</div>
		</td>
		</tr>
		</table>
	&nbsp;
	</div>
HTML;
return $HTML;
}

//*****************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS
#portfolio_info { font-size: 12px; text-align: left; width: 350px; float: right; }
#portfolio_category { display: inline-block; font-size: 12px; text-align: left; width: 300px;}
#portfolio_category b { font-family: Calibri;text-transform: uppercase; padding: 3px 0px 3px 0px;}
#portfolio_category li {list-style: none; line-height: 19px;width: 295px;}
#portfolio_category li a { color: #373737; text-decoration: underline;}
#portfolio_category li.sel, #portfolio_category li:hover { color: #F38833; cursor: pointer; }
#portfolio_category li:hover a, #portfolio_category li a:hover { color: #F38833; cursor: pointer; }
#portfolio_category li:hover, #portfolio_category li.sel {background: #EDE1CC url('../img/acp/submenu_arrow.png') no-repeat 100% 50%;}
#portfolio_category li .projects {display: none; color: #B8AD9A; font-size: 11px; padding: 0px 10px 0px 0px;margin: 0px;float: right;}
#portfolio_category li:hover .projects, #portfolio_category li.sel .projects { display: inline; }
#portfolio_content { font-size: 12px; text-align: left; width: 100%; }
#portfolio_content .link { height: 120px; padding: 3px 3px 3px 3px; width: 120px; }
#part_title, h2 {  color: #373737; font-size: 22px; font-family: Calibri, 'Trebuchet MS'; font-weight: normal; margin: 0px; padding: 0px; text-align: left; width: 100%; }
#portfolio_title { border-bottom: 1px solid #DA7A2E; color: #DA7A2E; font-family: Tahoma; font-size: 16px; font-weight: normal; line-height: normal; padding: 10px 0px 0px 0px; text-align: left; width: 100%; }
.portfolio_poster { background-color: #F7F2E5;border: 1px solid #D5CAB7; border-radius: 4px 4px 4px 4px; margin-right: 8px; -moz-border-radius: 4px 4px 4px 4px; padding: 5px; position: relative; vertical-align: top; -webkit-border-radius: 4px 4px 4px 4px; width: 100px; }
.portfolio_poster:hover { border: 1px solid #B8AD9A; background-color: #EDE1CC;}

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


//***********************************************
// 	{html:head} 
//	Заголовок портфолио (ФИО и Девиз)
function head($portfolio) {
$HTML = <<<HTML
	<div id="title" style="margin-bottom: 30px;">
		<div id="portfolio_info">
			{html:info}
		</div>
		<div style="font-size: 36px;margin-left: -2px;">
			{$portfolio['title']}
		</div>
		<div style="font-size: 12px;">
			{$portfolio['motto']}
		</div>
	</div>
HTML;
return $HTML;
}


//***********************************************
// 	{html:info} 
//	Информация
function info($info) {
$HTML = <<<HTML
	<br />Дата рождения: <b>{$info['birthdate']}</b> 
	<br />География: <b>{$info['country']},</b> <b>{$info['city']}</b>
	<br />E-mail: <b>{$info['email']}</b> ICQ: <b>{$info['icq']}</b> Телефон: <b>{$info['phone']}</b>

HTML;
return $HTML;
}


//***********************************************
// 	{html:part_title} 
//	Заголовок раздела
function part_title($info) {
$HTML = <<<HTML
	<h2>{$info}</h2>
HTML;
return $HTML;
}



//***********************************************
// 	{html:category} 
//	Дерево категорий
function category($category, $sel_cat_id=0) {
$f = "ов"; if($category['projects'] == 1) $f = ""; elseif($category['projects'] == 2 OR $category['projects'] == 3 OR $category['projects'] == 4) $f = "a";
if($sel_cat_id != 0 && $sel_cat_id == $category['cat_id']) $class = " class=\"sel\"";
else $class="";
if($category['type'] == "category") {
$HTML = <<<HTML
	<li{$class}>
		<a href="{THIS}?category={$category['cat_id']}" style="padding-left: {$category['padding']}px">{$category['cat_name']}</a> <font class="projects">{$category['projects']} проект{$f}</font>
	</li>
HTML;
}
elseif($category['type'] == "part") {
$HTML = <<<HTML
	<b style="padding-left: {$category['padding']}px; display: block;">
		{$category['cat_name']}
	</b>
HTML;
}
return $HTML;
}


//***********************************************
// 	{html:content} 
//	Отрисовка краткого представления проекта.
function brief_project($project) {
$HTML = <<<HTML

	<div id="portfolio_title">
		{$project['title']}
		<div style="font-size: 10px;line-height: 15px;padding-top: 0px;">{$project['sub_title']}</div>
	</div>

	<table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding-bottom: 15px;padding-top: 5px;">
		<tr>
			<td valign="top" style="vaertical-align: top;">
				<a href="{THIS}?project={$project['id']}" class="link" title="{$project['title']}"><img src="upload/{$project['poster']}" border="0" alt="{$project['title']}"></a>
				<div align="right"><a href="{THIS}?project={$project['id']}" class="link">Смотреть подробности проекта</a></div>
			</td>
		</tr>
	</table>

HTML;
return $HTML;
}


//***********************************************
// 	{html:content} 
//	Отрисовка полного представления проекта.
function project($project) {
$link = (!empty($project['link'])) ? "Ссылка: <a href=\"{$project['link']}\" rel=\"nofollow\" class=\"link\" target=\"_blank\">{$project['link']}</a>" : "" ;
$HTML = <<<HTML

	<h2>{$project['title']}</h2>
	<div id="portfolio_title" style="font-size: 11px;">
		{$project['sub_title']}
	</div>

	<table border="0" cellpadding="0" cellspacing="0" style="padding-bottom: 5px;padding-top: 5px;">
		<tr>
			<td valign="top" style="vaertical-align: top;">
				<img src="upload/{$project['poster']}" border="0" alt="{$project['title']}">
			</td>
		</tr>
	</table>
	
	{$link}
	
	
	{$project['steps']}

	<br /><div align="right"><a href="{THIS}?category={$project['category_id']}" class="link">Вернуться назад</a></div>
	<br />
	{module:vk_like} {module:google_plusone}
	{module:vk_comments}
	<br />
	<br />

HTML;
return $HTML;
}


//***********************************************
// отрисовка этапа
// {html:content} --> $project['steps'];
function project_step($step) {
$picture = (!empty($step['step_picture'])) ? "<img src=\"upload/{$step['step_picture']}\" border=\"0\" alt=\"рис. {$step['n']}\" title=\"рис. {$step['n']}\">": "";
$HTML = <<<HTML
	<p>
		<br />{$step['step_description']}
		<center>
			<br />{$picture}
		</center>
	</p>
HTML;
return $HTML;
}


//***********************************************
// Отрисовка облака тегов
// {html:tag_cloud}
function tag($fontsize, $key, $ukey, $value, $incat="") {
$HTML = <<<HTML
	<a href="{THIS}?tag={$ukey}" class="linkt"><span style="font-size:{$fontsize}%;" title="{$value}">{$key}</span></a>
HTML;
return $HTML;
}


//***********************************************
// Отрисовка раздела
// {html:content}
function part($part) {
$HTML = <<<HTML
	<br />&nbsp;&nbsp;&nbsp; <b style="padding-left: {$part['padding']}px">--</b> <a href="{THIS}?category={$part['cat_id']}" class="link">{$part['cat_name']}</a>
	<font class="projects">{$part['projects']} работ в категории</font>
HTML;
return $HTML;
}


//***********************************************
// 	{html:navpage} 
//	Отрисовка навигации по страницам
function navpage() {
$HTML = <<<HTML
	<div style="border-top: 3px dotted #B8AD9A;margin-top: 30px;padding-top: 10px;">Страница: {html:navpage_el}</div>
HTML;
return $HTML;
}


//***********************************************
// 	{html:navpage_el} 
//	Элмент навигации по страницам (ссылка на страницу)
function navpage_el($cat_id, $page, $part="category") {
$topage = ($page != 1) ? "&page=".$page : "" ;
$HTML = <<<HTML
	<b><a href="{THIS}?{$part}={$cat_id}{$topage}" class="linkb">{$page}</a></b>
HTML;
return $HTML;
}


//***********************************************
// 	{html:content} 
//	Вывод информации из опции "О себе"
//	Показывается когда не выбрано котегорий.
function about($about) {
$HTML = <<<HTML
	{$about}
HTML;
return $HTML;
}

}
?>