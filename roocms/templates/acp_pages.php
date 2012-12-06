<?php

class tpl_items_acp_pages {

//#####################################################
//#		Основной шаблон
//#####################################################

function tpl_page() {
$HTML = <<<HTML
<center>
	<table width="99%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td width="270" align="left" valign="top">
			<ul id="acp_submenu">
			<li class="part">Разделы</li>
			<li><a href="{THIS}?act=pages">Список страниц</a></li>

			</ul>

			<ul id="acp_submenu">
			<li class="part">Опции</li>
			<li><a href="{THIS}?act=pages&part=create&type=html">Создать страницу html</a></li>
			<li><a href="{THIS}?act=pages&part=create&type=php">Создать страницу php</a></li>

			</ul>
		</td>
		<td align="left" valign="top">
			<div id="content">
				{html:content}
			</div>
		</td>
		</tr>
	</table>
</center>
HTML;
return $HTML;
}

//*****************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS

.td_line { border-bottom: 1px dotted #F8AF48;  padding-left: 3px; padding-right: 3px; text-align: left; }
.td_line_option { border-bottom: 1px dotted #F8AF48; padding-left: 3px; height: 22px;}
#page_line			{ background-color: white; }
#page_line:hover	{ background-color: #EDE1CC;border-top: 1px solid #F38833;border-bottom: 1px solid #F38833; }

CSS;
return $CSS;
}

//****************************************************
// JS
function tpl_js() {
$JS = <<<JS
<script type="text/javascript" src="plugin/ckeditor/ckeditor.js"></script>
JS;
return $JS;
}

//#####################################################
//#		Элементы шаблона
//#####################################################


//************************
// {html:content}
function view_list_pages() {
$HTML = <<<HTML
	<div id="tabs">
	<ul>
		<li><a href="#pages">Страницы</a></li>
	</ul>
	<div id="pages">
	<noscript><h2> Список имеющихся страниц</h2></noscript>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="td_line" width="10"></td>
			<td class="td_line"><u>id</u></td>
			<td class="td_line"><u>Алиас страницы</u></td>
			<td class="td_line"><u>Название страницы</u></td>
			<td class="td_line"><u>Тип страницы</u></td>
			<td class="td_line"><u>Последнее обновление</u></td>
			<td class="td_line_option"><u>Опции</u></td>
		</tr>
			{html:page_brief}
		</table>
	</div>
	</div>
HTML;
return $HTML;
}


//************************
// {html:page_brief}
function page_brief($page) {
$HTML = <<<HTML
	<tr id="page_line">
		<td class="td_line" width="10" align="center"><b>{$page['default']}</b></td>
		<td class="td_line"><b>{$page['id']}</b></td>
		<td class="td_line"><b>{$page['alias']}</b></td>
		<td class="td_line">{$page['page_title']}</td>
		<td class="td_line">{$page['page_type']}</td>
		<td class="td_line">{$page['last_update']}</td>
		<td class="td_line_option">
			<a href="{THIS}?act=pages&part=edit&page={$page['id']}" class="link">Редактировать</a> | <a href="{THIS}?act=pages&part=delete&page={$page['id']}" class="link" title="Удалить">Удалить</a>
		</td>
	</tr>
HTML;
return $HTML;
}

//************************
//	Create HTML page
//	{html:content}
function create_html_page() {
$HTML = <<<HTML
	<div id="tabs">
	<ul>
	 <li><a href="#createhtml">Создать страницу</a></li>
	</ul>
	<div id="createhtml">
	<noscript><h2> Создать новую html страницу</h2></noscript>
		<form method="post" enctype="multipart/form-data">
			<table width="99%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="30%" align="left" valign="top">
						<b>Название:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="page_title">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Алиас:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="page_alias">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Мета описание:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="meta_description">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Мета ключевы слова:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="meta_keywords">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Контент:</b>
					</td><td align="right" valign="top">

					</td>
				</tr><tr>
					<td colspan="2">
						<textarea id="content_field" class="f_textarea" name="page_content"></textarea>
						<script>CKEDITOR.replace( 'content_field', {toolbar: 'RooCMS'});</script>
					</td>
				</tr><tr>
					<td align="right" valign="top" colspan="2">
						<input type="submit" name="create_page" class="f_submit" value="Создать страницу">
					</td>
				</tr>
			</table>
		</form>
	</div>
	</div>
HTML;
return $HTML;
}


//************************
//	Create PHP page
//	{html:content}
function create_php_page() {
$HTML = <<<HTML
	<div id="tabs">
	<ul>
	 <li><a href="#createhtml">Создать страницу</a></li>
	</ul>
	<div id="createhtml">
	<noscript><h2> Создать новую html страницу</h2></noscript>
		<form method="post" enctype="multipart/form-data">
			<table width="99%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="30%" align="left" valign="top">
						<b>Название:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="page_title">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Алиас:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="page_alias">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Мета описание:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="meta_description">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Мета ключевы слова:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="meta_keywords">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Контент:</b>
					</td><td align="right" valign="top">

					</td>
				</tr><tr>
					<td colspan="2">
						<textarea id="content_field" class="f_textarea" name="page_content"></textarea>
						
					</td>
				</tr><tr>
					<td align="right" valign="top" colspan="2">
						<input type="submit" name="create_page" class="f_submit" value="Создать страницу">
					</td>
				</tr>
			</table>
		</form>
	</div>
	</div>
HTML;
return $HTML;
}


//************************
//	Edit HTML page
//	{html:content}
function edit_html_page($page) {
$HTML = <<<HTML
	<div id="tabs">
	<ul>
	 <li><a href="#edithtml">Редактировать страницу</a></li>
	</ul>
	<div id="edithtml">
	<noscript><h2> Редактируем html страницу</h2></noscript>
		<form method="post" action="{THIS}?act=pages&part=update" enctype="multipart/form-data">
			<table width="99%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="30%" align="left" valign="top">
						<b>Название:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="page_title" value="{$page['page_title']}">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Алиас:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="page_alias" value="{$page['alias']}">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Мета описание:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="meta_description" value="{$page['meta_description']}">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Мета ключевые слова:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="meta_keywords" value="{$page['meta_keywords']}">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Контент:</b>
					</td><td align="right" valign="top">

					</td>
				</tr><tr>
				<td colspan="2">
						<textarea id="content_field" class="f_textarea" name="page_content">{$page['page_content']}</textarea>
						<script>CKEDITOR.replace( 'content_field', {toolbar: 'RooCMS'});</script>
						<input type="hidden" name="page_id" value="{$page['id']}">
				</td>
				</tr><tr>
					<td align="right" valign="top" colspan="2">
						<input type="submit" name="update_page" class="f_submit" value="Сохранить страницу">
					</td>
				</tr>
			</table>
		</form>
	</div>
	</div>
HTML;
return $HTML;
}


//************************
//	Edit PHP page
//	{html:content}
function edit_php_page($page) {
$HTML = <<<HTML
	<div id="tabs">
	<ul>
	 <li><a href="#edithtml">Редактировать страницу</a></li>
	</ul>
	<div id="edithtml">
	<noscript><h2> Редактируем html страницу</h2></noscript>
		<form method="post" action="{THIS}?act=pages&part=update" enctype="multipart/form-data">
			<table width="99%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="30%" align="left" valign="top">
						<b>Название:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="page_title" value="{$page['page_title']}">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Алиас:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="page_alias" value="{$page['alias']}">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Мета описание:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="meta_description" value="{$page['meta_description']}">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Мета ключевые слова:</b>
					</td><td align="right" valign="top">
						<input type="text" class="f_input" name="meta_keywords" value="{$page['meta_keywords']}">
					</td>
				</tr><tr>
					<td width="30%" align="left" valign="top">
						<b>Контент:</b>
					</td><td align="right" valign="top">

					</td>
				</tr><tr>
				<td colspan="2" align="right">
						<textarea id="content_field" class="f_textarea" name="page_content">{$page['page_content']}</textarea>
						<input type="hidden" name="page_id" value="{$page['id']}">
				</td>
				</tr><tr>
					<td align="right" valign="top" colspan="2">
						<input type="submit" name="update_page" class="f_submit" value="Сохранить страницу">
					</td>
				</tr>
			</table>
		</form>
	</div>
	</div>
HTML;
return $HTML;
}


// end class
}

?>