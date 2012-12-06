<?php

class tpl_items_acp_gallery{

//#########################################################
//#		Основной шаблон
//#########################################################

function tpl_page() {
$HTML = <<<HTML
<center>
	<table width="99%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td width="270" align="left" valign="top">
			<ul id="acp_submenu">
			<li class="part">Категории</li>
			{html:category}
			</ul>
		</td>
		<td align="left" valign="top">
				{html:content}
		</td>
		</tr>
	</table>
</center>
HTML;
return $HTML;
}

// CSS
function tpl_css() {
$CSS = <<<CSS

CSS;
return $CSS;
}

// JS
function tpl_js() {
$JS = <<<JS

JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################


//========================================
// {html:content}
function images($allprojects) {
($allprojects != 0) ? $sort = '&nbsp;<input type="submit" class="f_submit" name="update_sort" value="Сохранить порядок">' : $sort = "<b>В данном разделе нет изображений</b>" ;
$HTML = <<<HTML
	<div id="tabs">
		<ul>
		 <li><a href="#works">Изображение</a></li>
		 <li><a href="#newimage">Добавить изображение</a></li>
		 <li><a href="#addcat">Добавить раздел/Категорию</a></li>
		</ul>
		
		<div id="works">
			<div id="config_part">
			<noscript><h2>Изображения</h2></noscript>
				{html:navpage}
				<form method="post" action="{THIS}?act=gallery&part=sortimage">
				   {html:project}
				   <br />{$sort}
				</form>
			</div>
		</div>

		<div id="newimage">
			<div id="config_part">
			<noscript><h2>Добавить изображение</h2></noscript>
			{html:form_addimage}
			</div>
		</div>
		
		<div id="addcat">
			<div id="config_part">
			<noscript><h2>Добавить раздел / категорию:</h2></noscript>
				<form method="post" action="{THIS}?act=gallery&part=addcat">
					<table width="99%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="30%" align="left" valign="top">
								<b>Тип:</b>
							</td><td align="right" valign="top">
								<select name="type" class="f_input">
								<option value="category">Категория</option>
								<option value="part">Раздел</option>
								</select>
							</td>
						</tr><tr>
							<td width="30%" align="left" valign="top">
								<b>Родительская категория:</b>
							</td><td align="right" valign="top">
								<select name="cat_parent" class="f_input">
								<option value="0">Корень</option>
									{html:select_pcategory}
								</select>
							</td>
						</tr><tr>
							<td width="30%" align="left" valign="top">
								<b>Название:</b>
							</td><td align="right" valign="top">
								<input type="text" class="f_input" name="cat_name">
							</td>
						</tr><tr>
							<td align="right" valign="top" colspan="2">
								<input type="submit" name="new_category" class="f_submit" value="Добавить раздел / категорию">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
</div>	


HTML;
return $HTML;
}


//========================================
// {html:project} 
function project($image) {
$HTML = <<<HTML
<div class="unit_image">
	<a href="upload/gallery/{$image['original_img']}" rel="img"><img src="upload/gallery/{$image['thumb_img']}" class="unit_image" height="88" width="88"  alt="{$image['description']}" title="{$image['description']}"></a>
	<br />
	<input type="text" name="sort[{$image['id']}]" class="f_input_ms" value="{$image['sort']}" style="border: 1px solid #dae1e8; padding: 1px; width: 48px; " title="Порядок сортировки/вывода">
	<a href="{THIS}?act=gallery&part=editimage&image={$image['id']}" id="edit" alt="редактировать" title="редактировать"></a>&nbsp;
	<a href="{THIS}?act=gallery&part=delimage&image={$image['id']}" id="close" alt="удалить" title="удалить"></a>
</div>
HTML;
return $HTML;
}


//========================================
// {html:content}
function editcat($category, $selected="") {
if($category['type'] == "part") $selected = "selected";
$HTML = <<<HTML
	<div id="tabs">
		<ul>
			<li><a href="#editcategory">Редактировать категорию</a></li>
			<a href="{THIS}?act=gallery&category={$category['id']}" class="button">Вернутся в текущую категорию / раздел</a>
		</ul>
		<div id="editcategory">
	
			<form method="post" action="{THIS}?act=gallery&part=updatecat&category={$category['id']}" enctype="multipart/form-data">
			  <div id="config_part">
				<div id="part_title">Редактировать категорию</div>
					<table width="99%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="30%" align="left" valign="top">
								<b>Тип:</b>
							</td><td align="right" valign="top">
								<select name="type" class="f_input">
								<option value="category">Категория</option>
								<option value="part"{$selected}>Раздел</option>
								</select>
							</td>
						</tr><tr>
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
							<td align="right" valign="top" colspan="2">
								<input type="hidden" name="thisid" value="{$category['id']}">
								<input type="submit" name="update_cat" class="f_submit" value="Сохранить изменения">
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


//========================================
// {html:content} 
function editimage($image) {
$HTML = <<<HTML
<div id="tabs">
	<ul>
		<li><a href="#edit_image">Редактировать изображение</a></li>
		<a href="{THIS}?act=gallery&category={$image['category_id']}" class="button">Вернуться в категорию с изображениями</a>
	</ul>
	<div id="edit_image">
		<div id="config_part">
		<noscript><h2>Редактировать изображение:</h2></noscript>
			<form method="post" action="{THIS}?act=gallery&part=updateimage" enctype="multipart/form-data">	
			<table width="99%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="30%" align="left" valign="top">
					</td><td align="left" valign="top">
						<a href="upload/gallery/{$image['original_img']}" rel="img" class="linku">Посмотреть изображение</a>
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="top">
						<b>Категория:</b>
					</td><td align="right" valign="top">
						<select name="category" class="f_input">
							{html:select_pcategory}
						</select>
						<input type="hidden" name="prev_cat" value="{$image['category_id']}">
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="top">
						<b>Описание изображения:</b>
					</td><td align="right" valign="top">
						<textarea class="f_textarea" style="height: 42px;" name="description">{$image['description']}</textarea>
					</td>
				</tr>
				<tr>
					<td width="30%" align="left" valign="top">
						<b>Перезалить изображения:</b>
						<font class="rem"><br />Оставьте поле пустым, если не хотите заменить/обновить изображение.</font>
					</td><td align="right" valign="top">
						<input type="file" name="image" class="f_input" size="47">
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<input type="hidden" name="id" value="{$image['id']}">
						<input type="submit" name="update_image" class="button" value="Сохранить изменения">
					</td>
				</tr>
			</table>
			</form>
		</div>
	</div>
</div>
HTML;
return $HTML;
}


//========================================
// {html:category} 
function category($category) {
global $GET; 
(isset($GET->_category) && $GET->_category == $category['cat_id']) ? $class = ' class="sel"' : $class="" ;
$category['padding'] = ceil($category['padding'] / 2);
$HTML = <<<HTML

	<li{$class}><nobr>
	<a href="{THIS}?act=gallery&category={$category['cat_id']}" style="padding-left: {$category['padding']}px;">{$category['cat_name']}</a>
	<a href="{THIS}?act=gallery&part=editcat&category={$category['cat_id']}" id="edit" title="Редактировать"></a>
	<a href="{THIS}?act=gallery&part=delcat&category={$category['cat_id']}" id="close" title="Удалить"></a>
	</nobr></li>

HTML;
return $HTML;
}


//========================================
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

//========================================
// {html:select_pwcategory} 
function select_pwcategory($cat_id, $cat_name, $cat_type, $level=0, $indention=15, $parent="", $selected="", $dot="") {
	global $GET;
	for($l=0;$l<=$level/$indention;$l++) {$dot .= "&nbsp;&nbsp;";}
	if(isset($GET->_category) && $GET->_category == $cat_id && $parent == "") $selected = " selected";
	elseif($parent >= 0 && $cat_id == $parent) $selected = " selected";
if($cat_type == "part") {
$HTML = <<<HTML
	<optgroup label="{$dot}{$cat_name}"></optgroup>
HTML;
}
else {
$HTML = <<<HTML
	<option value="{$cat_id}"{$selected}>{$dot}{$cat_name}</option>
HTML;
}
return $HTML;
}


//========================================
// {html:form_addimage}
function form_addimage($uploadmaxfilesize) {
$HTML = <<<HTML
	   <form method="post" action="{THIS}?act=gallery&part=addimage" enctype="multipart/form-data">
		<table width="99%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="30%" align="left" valign="top">
					<b>Категория:</b>
				</td><td align="right" valign="top">
					<select name="category" class="f_input">
						{html:select_pwcategory}
					</select>
				</td>
			</tr>
			<tr>
				<td width="30%" align="right" valign="top">
					<span style="cursor: pointer; background-color: #F2F4FF;" id="addimg">добавить ещё картинку</span>
					<script>
						$('#addimg').click(function() {
							$('<input type="file" name="image[]" size="47" class="f_input"></br>').appendTo('#moreimage');
						});
					</script>
				</td><td align="left" valign="top">
					&nbsp;Загрузить картинку: 
					<br /><input type="file" name="image[]" class="f_input" size="47">
					<div id="moreimage"></div>
				</td>
			</tr>
			<tr>
				<td align="right" valign="top" colspan="2">
					<input type="submit" name="add_news" class="button" value="Добавить изображения">
				</td>
			</tr>
		</table>
		<font class="rem">* Общий размер загружаемых за один раз изображений не должен превышать {$uploadmaxfilesize}
		<br />Иначе изображения не загрузятся. </font>
	   </form>
HTML;
return $HTML;
}


//========================================
// {html:form_addimage}
function form_not_addimage() {
$HTML = <<<HTML
	<br /><b>Нельзя добавлять изображение в выбранную категорию</b>
HTML;
return $HTML;
}


//========================================
// {html:navpage}
function navpage() {
$HTML = <<<HTML
	<div id="pad">Страницы: {html:navpage_el}</div>
HTML;
return $HTML;
}


//========================================
// {html:navpage_el} 
function navpage_el($cat_id, $page) {
$HTML = <<<HTML
	<b><a href="{THIS}?act=gallery&category={$cat_id}&page={$page}" class="linkb">&nbsp;{$page}&nbsp;</a></b>
HTML;
return $HTML;
}


// end class
}

?>