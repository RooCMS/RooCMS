<?php

class tpl_items_acp_news {

//#####################################################
//#		Основной шаблон
//#####################################################

function tpl_page() {
global $GET;
$sc = (isset($GET->_part) && $GET->_part == "sortcategory") ? ' class="sel"' : '' ;
$HTML = <<<HTML
<center>
	<table width="99%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td width="270" align="left" valign="top">
			<ul id="acp_submenu">
				<li class="part">Категории новостей</li>
				{html:category}
				<li class="part">Опции</li>
				<li{$sc}><a href="{THIS}?act=news&part=sortcategory">Сортировать категории</a></li>
			</ul>
			<div id="block_text" style="width: 260px;">
			<b>Статистика</b>
			<br />Всего категорий: <b>{html:total_cats}</b>
			<br />Всего новостей: <b>{html:total_news}</b>
			<br />Показано новостей: <b>{html:total_vnews}</b></div>
		</td>
		<td align="left" valign="top">
			<div align="right">{html:current_time}</div>
			{html:content}
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

#news_item {padding: 10px;border-bottom: 3px dotted #dae1e8;}

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

//============================
//	{html:current_time}
function current_time($date) {
$HTML = <<<HTML

	<b>Сегодня:</b> {$date}

HTML;
return $HTML;
}


//===============================================
//	{html:content}
function idx() {
$HTML = <<<HTML

	<div id="tabs">
		<ul>
			<li><a href="#view_news">Новости</a></li>
			<li><a href="#new_news">Добавить новость</a></li>
			<li><a href="#add_category">Добавить категорию</a></li>
		</ul>
		<div id="view_news">
			<noscript><h2>Новости</h2></noscript>
			{html:navpage} 
			{html:newslist}
		</div>
		<div id="new_news">
			<noscript><h2>Добавить новость</h2></noscript>
			{html:addnews}
		</div>
		<div id="add_category">
			<noscript><h2>Добавить категорию</h2></noscript>
			{html:addcategory}
		</div>
	</div>

HTML;
return $HTML;
}


//===============================================
// {html:newslist} 
function newslist($news) {
$HTML = <<<HTML

	<div id="news_item">
		<font class="title_text">{$news['title']}</font>
		<br /><font class="date">Дата показа: <b>{$news['rdate']}</b></font> [<font class="date">Cоздана: {$news['date_create']}</font> | <font class="date">Обновлена: {$news['date_update']}</font>]
		
		<div id="showhide-{$news['id']}" style="cursor: pointer;">
			<img src="img/acp/plus_16.png" class="img">смотреть
		</div>
		<div id="full-{$news['id']}" style="display: none;">
			<div id="block_text" class="corner">{$news['brief_news']}</div>
			<div id="block_text" class="corner">{$news['full_news']}</div>
			<font class="rem">
				Изображений: {$news['images']} | Файлов: {$news['files']}
			</font>
		</div>
		
		<div align="right">
			<a href="{THIS}?act=news&part=editnews&news={$news['id']}" class="link">редактировать</a>
			<a href="{THIS}?act=news&part=delnews&news={$news['id']}" class="link">удалить</a>
		</div>
	</div>
	
	<script>
		$('#showhide-{$news['id']}').click(function() {
		  $('#full-{$news['id']}').slideToggle(1000);
		});
	</script>

HTML;
return $HTML;
}


//===============================================
// {html:content} 
function editnews($news) {
$HTML = <<<HTML
	<div id="tabs">
		<ul>
			<li><a href="#edit_news">Редактировать новость</a></li>
			<a href="{THIS}?act=news&category={$news['category_id']}" class="button">Вернуться к списку новостей</a>
		</ul>
		<div id="edit_news">

	<form method="post" action="{THIS}?act=news&part=updatenews" enctype="multipart/form-data">
	
	<noscript><h2>Редактировать новость</h2></noscript>
	
		<table width="99%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="30%" align="left" valign="top">
					<b>Название:</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="title" value="{$news['title']}">
				</td>
			</tr>
			<tr>
				<td width="30%" align="left" valign="top">
					<b>Категория:</b>
				</td><td align="right" valign="top">
					<select name="category" class="f_input">
						{html:select_pcategory}
					</select>
				</td>
			</tr>
			<tr>
				<td width="30%" align="left" valign="top">
					<b>Мета описание:</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="meta_description" value="{$news['meta_description']}">
				</td>
			</tr>
			<tr>
				<td width="30%" align="left" valign="top">
					<b>Мета ключевые слова:</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="meta_keywords" value="{$news['meta_keywords']}">
				</td>
			</tr>
			<tr>
				<td width="30%" align="left" valign="top">
					<b>Дата начала показа:</b>
				</td><td align="right" valign="top">
					Новость добавлена: {$news['date_create']}
					<br />Новость обновлена: {$news['date_update']}
					<br />
					 <select name="day" class="f_input_s">
										{$news['day']}
									 </select>
									 <select name="month" class="f_input_m">
										{$news['month']}
									 </select>
									 <select name="year" class="f_input_s">
										{$news['year']}
									 </select>
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top" colspan="2">
					<b>Краткая новость:</b>
				</td>
			</tr><tr>
				<td align="right" valign="top" colspan="2">
					<textarea class="ckeditor" name="brief_news">{$news['brief_news']}</textarea>
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top" colspan="2">
					<br /><b>Полная новость:</b>
				</td>
			</tr><tr>
				<td align="right" valign="top" colspan="2">
					<textarea class="ckeditor" name="full_news">{$news['full_news']}</textarea>
				</td>
			</tr>
		</table>
		<table width="99%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td align="left">
					<br /><b>Картинки новости:</b> <font class="rem">[<a href="{THIS}?act=index#filetypes" target="_blank" title="Посмотреть допустимые размеры и форматы файлов">?</a>]</font>
				</td>
				<td align="left">
					<br /><b>Файлы новости:</b> <font class="rem">[<a href="{THIS}?act=index#filetypes" target="_blank" title="Посмотреть допустимые размеры и форматы файлов">?</a>]</font>
				</td>
			</tr><tr>
				<td width="50%">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					
						{html:images}
						
					</table>
				</td>
				<td width="50%">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					
						{html:files}
						
					</table>
				</td>
			</tr><tr>
				<td width="50%" valign="top">
					&nbsp;Загрузить картинку: <input type="file" name="image[]" class="f_input" size="47">
					<div id="moreimage"></div>
					<span style="cursor: pointer; background-color: #F2F4FF;" id="addimg">добавить ещё картинку</span>
					<script>
						$('#addimg').click(function() {
							$('<input type="file" name="image[]" size="47" class="f_input"></br>').appendTo('#moreimage');
						});
					</script>
				</td>
				<td width="50%" valign="top">
					&nbsp;Загрузить файл: <input type="file" name="file[]" class="f_input" size="47">
					<div id="morefile"></div>
					<span style="cursor: pointer; background-color: #F2F4FF;" id="addfile">добавить ещё файл</span>
					<script>
						$('#addfile').click(function() {
							$('<input type="file" name="file[]" size="47" class="f_input"></br>').appendTo('#morefile');
						});
					</script>
				</td>
			</tr><tr>
				<td colspan="2" align="right">
					<br />
					<input type="hidden" name="id" value="{$news['id']}">
					<input type="submit" name="update_news" class="button" value="Сохранить новость">
					<a href="{THIS}?act=news" class="button">Вернуться к списку новостей</a>
				</td>
			</tr>
		</table>
	</form>

	
		</div>
	</div>

HTML;
return $HTML;
}


//===============================================
// {html:content}
function editcat($category) {
$HTML = <<<HTML
	<div id="tabs">
		<ul>
			<li><a href="#edit">Редактировать категорию</a></li>
			<a href="{THIS}?act=news&category={$category['id']}" class="button">Вернутся в текущую категорию / раздел</a>
		</ul>
		<div id="edit">
		
			<form method="post" action="{THIS}?act=news&part=updatecat&category={$category['id']}" enctype="multipart/form-data">
			  <div id="config_part">
			  
				<noscript><h2>Редактировать категорию</h2></noscript>
					
					<table width="99%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="30%" align="left" valign="top">
								<b>Родительская категория:</b>
							</td><td align="right" valign="top">
								<select name="parent_category" class="f_input">
								<option value="0">без категории</option>
								{html:select_pcategory}
								</select>
							</td>
						</tr><tr>
							<td width="30%" align="left" valign="top">
								<b>Название: </b>
							</td><td align="right" valign="top">
								<input type="text" class="f_input" name="title" value="{$category['name']}">
							</td>
						</tr><tr>
							<td width="30%" align="left" valign="top">
								<b>Мета Описание</b>
							</td><td align="right" valign="top">
								<input type="text" class="f_input" name="meta_description" value="{$category['meta_description']}">
							</td>
						</tr><tr>
							<td width="30%" align="left" valign="top">
								<b>Мета ключевые слов</b>
							</td><td align="right" valign="top">
								<input type="text" class="f_input" name="meta_keywords" value="{$category['meta_keywords']}">
							</td>
						</tr><tr>
							<td align="right" valign="top" colspan="2">
								<input type="hidden" name="thisid" value="{$category['id']}">
								<input type="submit" name="update_cat" class="button" value="Сохранить изменения">
							</td>
						</tr>
					</table>
			  </div>
			</form>
		
		</div>
	</div>
HTML;
return $HTML;
}


//===============================================
// {html:images}
function images($image) {
$HTML = <<<HTML
	<tr>
		<td width="50" align="right" valign="top">
			<a href="upload/{$image['original_img']}" rel="img"><img src="upload/{$image['thumb_img']}" class="unit_image" width="32" height="32"></a>
			<a href="{THIS}?act=news&part=delimage&image={$image['id']}" class="delimage" title="Удалить изображение. ВНИМАНИЕ! Все несохраненные данные будут утеряны."></a>
		</td><td align="right" valign="top">
			<textarea class="f_textarea" name="editimage[{$image['id']}]" style="height: 34px;">{$image['description']}</textarea>
		</td>
	</tr>
HTML;
return $HTML;
}


//===============================================
// {html:files}
function files($file) {
$HTML = <<<HTML
	<tr>
		<td width="50" align="right" valign="top">
			<a href="upload/files/{$file['filename']}"><img src="img/icon/32/{$file['icon']}" class="unit_image" width="32" height="32"></a>
			<a href="{THIS}?act=news&part=delfile&file={$file['id']}" class="delimage" title="Удалить файл. ВНИМАНИЕ! Все несохраненные данные будут утеряны."></a>
		</td><td align="right" valign="top">
			<textarea class="f_textarea" name="editfile[{$file['id']}]" style="height: 34px;">{$file['description']}</textarea>
		</td>
	</tr>
HTML;
return $HTML;
}


//===============================================
// {html:navpage} 
function navpage() {
$HTML = <<<HTML
	Страницы: {html:navpage_el}
HTML;
return $HTML;
}


//===============================================
// {html:navpage_el} 
function navpage_el($page, $category) {
$HTML = <<<HTML
	<b><a href="{THIS}?act=news&category={$category}&page={$page}" class="pages">{$page}</a></b>
HTML;
return $HTML;
}


//===============================================
//	{html:addnews}
function addnews($new) {
$HTML = <<<HTML

	   <form method="post" action="{THIS}?act=news&part=addnews" enctype="multipart/form-data">

		<table width="99%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="30%" align="left" valign="top">
					<b>Название</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="title">
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Категория:</b>
				</td><td align="right" valign="top">
					<select name="category" class="f_input">
						{html:select_pcategory}
					</select>
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Мета описание:</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="meta_description">
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Мета ключевые слова:</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="meta_keywords">
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Дата: </b>
				</td><td align="right" valign="top">
					<select name="day" class="f_input_s">
								{$new['day']}
							 </select>
							 <select name="month" class="f_input_m">
								{$new['month']}
							 </select>
							 <select name="year" class="f_input_s">
								{$new['year']}
							 </select>
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top" colspan="2">
					<b>Краткая новость:</b>
				</td>
			</tr><tr>
				<td align="right" valign="top" colspan="2">
					<textarea class="ckeditor" name="brief_news"></textarea>
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top" colspan="2">
					<br /><b>Полная новость:</b>
				</td>
			</tr><tr>
				<td align="right" valign="top" colspan="2">
					<textarea class="ckeditor" name="full_news"></textarea>
				</td>
			</tr><tr>
				<td align="left" valign="top" colspan="2">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
					<td width="50%" valign="top">
						&nbsp;Загрузить картинку: 
						<br /> <input type="file" name="image[]" class="f_input" size="47">
						<div id="moreimage"></div>
						<span style="cursor: pointer; background-color: #F2F4FF;" id="addimg">добавить ещё картинку</span>
						<script>
							$('#addimg').click(function() {
								$('<input type="file" name="image[]" size="47" class="f_input"></br>').appendTo('#moreimage');
							});
						</script>
					</td>
					<td width="50%" valign="top">
						&nbsp;Загрузить файл: 
						<br /><input type="file" name="file[]" class="f_input" size="47">
						<div id="morefile"></div>
						<span style="cursor: pointer; background-color: #F2F4FF;" id="addfile">добавить ещё файл</span>
						<script>
							$('#addfile').click(function() {
								$('<input type="file" name="file[]" size="47" class="f_input"></br>').appendTo('#morefile');
							});
						</script>
					</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align="right" valign="top" colspan="2">
					<input type="submit" name="add_news" class="button" value="Добавить новость">
				</td>
			</tr>
		</table>
	   </form>


HTML;
return $HTML;
}


//===============================================
// {html:addcategory}
function addcategory() {
$HTML = <<<HTML
	
	<form method="post" action="{THIS}?act=news&part=addcategory">
		<table width="99%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="30%" align="left" valign="top">
					<b>Категория:</b>
				</td><td align="right" valign="top">
					<select name="cat_parent" class="f_input">
					<option value="0">Корень</option>
						{html:select_pcategory}
					</select>
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Название</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="title">
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Мета Описание</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="meta_description">
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Мета ключевые слов</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="meta_keywords">
				</td>
			</tr><tr>
				<td align="right" valign="top" colspan="2">
					<input type="submit" name="add_category" class="button" value="Добавить категорию">
				</td>
			</tr>
		</table>
	</form>
	
HTML;
return $HTML;
}


//===============================================
//	{html:content} Сортировка новостей
function sortcategory() {
$HTML = <<<HTML

	<div id="tabs">
		<ul>
			<li><a href="#sort_category">Сортировать порядок категорий</a></li>
		</ul>
		<div id="sort_category">
		<form method="post" action="{THIS}?act=news&part=updatesortcategory">
		<noscript><h2>Сортировать порядок категорий</h2></noscript>
			{html:scategory}
			<div align="right">
				<input type="submit" name="update_sort_category" class="button" value="Сохранить изменный порядок">
			</div>
		</form>
		</div>
	</div>

HTML;
return $HTML;
}


//===============================================
// {html:category} 
function category($category) {
global $GET; 
(isset($GET->_category) && $GET->_category == $category['cat_id']) ? $class = ' class="sel"' : $class="" ;
$category['padding'] = ceil($category['padding'] / 2);
$HTML = <<<HTML

	<li{$class}>
	<nobr>
		<a href="{THIS}?act=news&category={$category['cat_id']}" style="padding-left: {$category['padding']}px;">{$category['cat_name']}</a>
		<a href="{THIS}?act=news&part=editcat&category={$category['cat_id']}" id="edit" title="Редактировать"></a>
		<a href="{THIS}?act=news&part=delcat&category={$category['cat_id']}" id="close" title="Удалить"></a>
	</nobr>
	</li>

HTML;
return $HTML;
}


//===============================================
// {html:select_pcategory} 
function select_pcategory($cat_id, $cat_name, $level=0, $indention=15, $parent="", $selected="", $dot="") {
	global $GET;
	for($l=0;$l<=$level/$indention;$l++) {$dot .= "&nbsp;&nbsp;";}
	if(isset($GET->_category) && $GET->_category == $cat_id && $parent == "") $selected = " selected";
	elseif($parent >= 0 && $cat_id == $parent) $selected = " selected";
$HTML = <<<HTML
	<option value="{$cat_id}"{$selected}>{$dot}{$cat_name}</option>
HTML;
return $HTML;
}


//===============================================
//	{html:scategory}
function scategory($category) {
$HTML = <<<HTML
	<input type="text" name="id[{$category['cat_id']}]" value="{$category['sort']}" class="f_input_ms">
	<b style="padding-left: {$category['padding']}px;">{$category['cat_name']}</b>
	<br />
HTML;
return $HTML;
}


//===============================================
// {html:addnews} && {html:newslist} for category_id = 0
function choosecategory() {
$HTML = <<<HTML
	<b>Перейдите в одну из категорий, что бы воспользоваться её содержимым.</b>
HTML;
return $HTML;
}


// end calss
}

?>