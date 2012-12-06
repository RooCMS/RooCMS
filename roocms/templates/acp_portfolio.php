<?php

class tpl_items_acp_portfolio{

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

#acp_portfolio_project { border-bottom: 1px solid #dae1e8; border-left: 0px; border-right: 0px; border-top: 0px solid #dae1e8; display: inline-block; font-family: 'Trebuchet MS'; margin: 0px 5px 1px 5px; padding: 5px; vertical-align: middle; width: 98%; }
#acp_portfolio_project .description 	{ vertical-align: top; }
#acp_portfolio_project .title	{ font-size: 24px; }

.ui-autocomplete {
	max-height: 200px;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
	/* add padding to account for vertical scrollbar */
	padding-right: 20px;
}
/* IE 6 doesn't support max-height
 * we use height instead, but this forces the menu to always be this tall
 */
* html .ui-autocomplete {height: 100px;}

CSS;
return $CSS;
}

// JS
function tpl_js() {
$JS = <<<JS
<script type="text/javascript" src="plugin/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	var n = 1;
	var s = 0;
	
	$(document).ready(function() {
		$(".checkbox").button({
			icons: {secondary: "ui-icon-closethick"}
		})
		.click(
			function () {
				var options;
				if($(this).is(':checked') == false) {
					options = {
						text: true,
						icons: {secondary: "ui-icon-closethick"}
					};
				}
				else {
					options = {
						text: true,
						icons: {secondary: "ui-icon-check"}
					};
				}
				$(this).button( "option", options );
			}
		);
	});
</script>
JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################


//========================================
// {html:content}
function allprojects($allprojects) {
($allprojects != 0) ? $sort = '&nbsp;<input type="submit" class="f_submit" name="update_sort" value="Сохранить порядок">' : $sort = "<br /><b>В данном разделе нет работ</b>" ;
$HTML = <<<HTML
	<div id="tabs">
		<ul>
		 <li><a href="#projects">Работы</a></li>
		 <li><a href="#newproject">Добавить новую работу</a></li>
		 <li><a href="#addcat">Добавить раздел/Категорию</a></li>
		</ul>
		
		<div id="projects">
			<div id="config_part">
			<noscript><h2>Работы</h2></noscript>
				{html:navpage}
				<form method="post" action="{THIS}?act=portfolio&part=sortwork">
				   {html:project}
				   {$sort}
				</form>
				
				<br />{html:navpage}
			</div>
		</div>

		<div id="newproject">
			{html:form_addwork}
		</div>
		
		<div id="addcat">
			<div id="config_part">
			<noscript><h2>Добавить раздел / категорию:</h2></noscript>
				<form method="post" action="{THIS}?act=portfolio&part=addcat">
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
// {html:content}
function editproject($project) {
$poster = "";
if(!empty($project['poster'])) $poster = <<<HTML
	<a href="upload/{$project['poster']}" class="linku" rel="img">Обложка проекта</a>
HTML;
$HTML = <<<HTML
	<div id="tabs">
		<ul>
			<li><a href="#editproject">Редактировать работу</a></li>
			<a href="{THIS}?act=portfolio&category={$project['category_id']}" class="button">Вернутся в текущую категорию</a>
		</ul>
		<div id="editproject">
			<form method="post" action="{THIS}?act=portfolio&part=update_project" enctype="multipart/form-data">
			  <div id="config_part">
				<noscript><h2>Редактировать работу</h2></noscript>
				<div align="right">
						<table width="99%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="30%" align="left" valign="top">
									<b>Название: </b>
								</td><td align="right" valign="top">
									<input type="text" class="f_input" name="title" value="{$project['title']}">
								</td>
							</tr><tr>
								<td width="30%" align="left" valign="top">
									<b>Второе название: </b>
								</td><td align="right" valign="top">
									<input type="text" class="f_input" name="sub_title" value="{$project['sub_title']}">
								</td>
							</tr><tr>
								<td width="30%" align="left" valign="top">
									<b>Категория:</b>
								</td><td align="right" valign="top">
									<select name="category" class="f_input">
									{html:select_pwcategory}
									</select>
								</td>
							</tr><tr>
								<td width="30%" align="left" valign="top">
									<b>Ссылка:</b>
								</td><td align="right" valign="top">
									<input type="text" class="f_input" name="link" value="{$project['link']}">
								</td>
							</tr><tr>
								<td width="30%" align="left" valign="top">
									<b>Постер (обложка проекта):</b>
								</td><td align="left" valign="top">
									{$poster}<input type="file" class="f_input" name="poster" size="80" title="Оставьте поле пустым если Вы не хотите заменять обложку">
								</td>
							</tr><tr>
								<td width="30%" align="left" valign="top">
									<b>Теги: </b>
								</td><td align="right" valign="top">
									<input type="text" class="f_input" id="tags" name="tags" value="{$project['tags']}">
									<input type="hidden" name="id" value="{$project['id']}">
									<input type="hidden" name="category_id" value="{$project['category_id']}">
								</td>
							</tr>

							<tr>
								<td width="100%" colspan="2" align="left">
									{$project['steps']}
									
									<div id="morestep"></div>
									<br /><span style="cursor: pointer; background-color: #F2F4FF;" id="addstep">добавить ещё этап</span>
									<script>
										$('#addstep').click(function() {
											$('<br /><b>Этап: <input type="text" name="new_step[]" class="inp_s" size="3" value="'+s+'"></b> <input type="file" name="new_step_picture[]" class="f_input" size="47"><br /><textarea id="step'+s+'" class="ckeditor" name="new_step_description[]"></textarea>').appendTo('#morestep');
											CKEDITOR.replace('step'+s);
											s = s + 1;
										});
										
									</script>
								</td>
							</tr>
							
							<tr>
								<td align="right" valign="top" colspan="2">
									<input type="submit" name="update_project" class="f_submit" value="Сохранить изменения в проекте">
								</td>
							</tr>
						</table>

				</div>
			  </div>
			</form>
			
			<script>
			$(function() {
				var availableTags = [
					{html:tags}
					""
				];
				function split( val ) {
					return val.split( /,\s*/ );
				}
				function extractLast( term ) {
					return split( term ).pop();
				}

				$( "#tags" )
					// don't navigate away from the field on tab when selecting an item
					.bind( "keydown", function( event ) {
						if ( event.keyCode === $.ui.keyCode.TAB &&
								$( this ).data( "autocomplete" ).menu.active ) {
							event.preventDefault();
						}
					})
					.autocomplete({
						minLength: 0,
						source: function( request, response ) {
							// delegate back to autocomplete, but extract the last term
							response( $.ui.autocomplete.filter(
								availableTags, extractLast( request.term ) ) );
						},
						focus: function() {
							// prevent value inserted on focus
							return false;
						},
						select: function( event, ui ) {
							var terms = split( this.value );
							// remove the current input
							terms.pop();
							// add the selected item
							terms.push( ui.item.value );
							// add placeholder to get the comma-and-space at the end
							terms.push( "" );
							this.value = terms.join( ", " );
							return false;
						}
					});
			});
			</script>
			<style>
			.ui-autocomplete {
				max-height: 200px;
				overflow-y: auto;
				/* prevent horizontal scrollbar */
				overflow-x: hidden;
				/* add padding to account for vertical scrollbar */
				padding-right: 20px;
			}
			/* IE 6 doesn't support max-height
			 * we use height instead, but this forces the menu to always be this tall
			 */
			* html .ui-autocomplete {
				height: 100px;
			}
			</style>
			
		</div>
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
			<a href="{THIS}?act=portfolio&category={$category['id']}" class="button">Вернутся в текущую категорию / раздел</a>
		</ul>
		<div id="editcategory">
	
			<form method="post" action="{THIS}?act=portfolio&part=updatecat&category={$category['id']}" enctype="multipart/form-data">
			  <div id="config_part">
				<noscript><h2>Редактировать категорию</h2></noscript>
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
// {html:project} 
function project($project) {
$HTML = <<<HTML

	<div id="acp_portfolio_project">
	<input type="text" name="sort[{$project['id']}]" class="f_input_ms" value="{$project['sort']}"><font class="title_text">
	{$project['title']} 
	<br /><font style="font-size:13px;font-weight: bold;">{$project['sub_title']}</font></font>
	<br /><img src="upload/{$project['poster']}">
	<div align="right">
		<a href="{THIS}?act=portfolio&part=edit_project&project={$project['id']}" class="link">редактировать</a>
		<a href="{THIS}?act=portfolio&part=del_project&project={$project['id']}" class="link">удалить</a>
	</div>
	</div>

HTML;
return $HTML;
}


//========================================
// {html:steps} --> $project['steps']
function step($step) {
if(!empty($step['step_picture'])) $picture = <<<HTML
	<br />
	<span class="ui-widget-header ui-corner-all" style="padding: 4px 0px 6px 5px;">
		<a href="upload/{$step['step_picture']}" rel="img" class="linku" title="Нажмите на ссылку, что бы посмотреть изображение">Изображение этапа</a> 
		<label class="label" for="del_step_picture[{$step['id']}]">Удалить изображение: </label><input type="checkbox" id="del_step_picture[{$step['id']}]" class="checkbox" name="del_step_picture[{$step['id']}]">
	</span>
HTML;
else $picture = "<b><i>нет изображения</i></b>";
$HTML = <<<HTML

	<br /><b>Этап: <input type="text" name="step[{$step['id']}]" class="inp_s" size="3" value="{$step['step']}"></b> {$picture} <input type="file" id="change_step_picture_{$step['id']}" name="step_picture[{$step['id']}]" class="f_input" size="47">
	<br /><textarea class="ckeditor" name="step_description[{$step['id']}]">{$step['step_description']}</textarea>
	<div align="right">
		<label class="label" for="del_step[{$step['id']}]">Удалить этап: </label><input type="checkbox" id="del_step[{$step['id']}]" class="checkbox" name="del_step[{$step['id']}]">
	</div>
	<input type="hidden" name="step_id[{$step['id']}]" value="{$step['id']}">
	<script>s = s + 1;</script>
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
	<a href="{THIS}?act=portfolio&category={$category['cat_id']}" style="padding-left: {$category['padding']}px;">{$category['cat_name']}</a>
	<a href="{THIS}?act=portfolio&part=editcat&category={$category['cat_id']}" id="edit" title="Редактировать"></a>
	<a href="{THIS}?act=portfolio&part=delcat&category={$category['cat_id']}" id="close" title="Удалить"></a>
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
// {html:form_addwork}
function form_addwork() {
$HTML = <<<HTML
	<form method="post" action="{THIS}?act=portfolio&part=addwork" enctype="multipart/form-data">
	  <div id="config_part">
		<noscript><h2>Добавить работу</h2></noscript>
		<table width="99%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="30%" align="left" valign="top">
					<b>Название:</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="title">
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Второе название: (слоган, краткое описание)</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="sub_title">
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Категория:</b>
				</td><td align="right" valign="top">
						<select name="category" class="f_input">
						{html:select_pwcategory}
						</select>
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Теги:</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" id="tags" name="tags">
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Ссылка:</b>
				</td><td align="right" valign="top">
					<input type="text" class="f_input" name="link">
				</td>
			</tr><tr>
				<td width="30%" align="left" valign="top">
					<b>Постер: (обложка)</b>
				</td><td align="left" valign="top">
					<input type="file" name="poster" class="f_input" size="80">
				</td>
			</tr><tr>
				<td width="100%" align="left" valign="top" colspan="2">
					<br /><b>Этап: <input type="text" name="step[]" class="inp_s" size="3" value="0"></b> <input type="file" name="step_picture[]" class="f_input" size="47">
					<br /><textarea class="ckeditor" name="step_description[]"></textarea>
					<div id="morestep"></div>
					<br /><span style="cursor: pointer; background-color: #F2F4FF;" id="addstep">добавить ещё этап</span>
					<script>
						$('#addstep').click(function() {
							$('<br /><b>Этап: <input type="text" name="step[]" class="inp_s" size="3" value="'+n+'"></b> <input type="file" name="step_picture[]" class="f_input" size="47"><br /><textarea id="step'+n+'" class="ckeditor" name="step_description[]"></textarea>').appendTo('#morestep');
							CKEDITOR.replace('step'+n);
							n = n + 1;
						});
						
					</script>
				</td>
			</tr><tr>
				<td align="right" valign="top" colspan="2">
					<input type="submit" name="add_project" class="button" value="Добавить проект">
				</td>
			</tr>
		</table>
	  </div>
	</form>
	
	<script>
	$(function() {
		var availableTags = [
			{html:tags}
			""
		];
		function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}

		$( "#tags" )
			// don't navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					response( $.ui.autocomplete.filter(
						availableTags, extractLast( request.term ) ) );
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( ", " );
					return false;
				}
			});
	});
	</script>
	
HTML;
return $HTML;
}


//========================================
// {html:tags}
function tags($tag) {
$HTML = <<<HTML
	"{$tag}",
HTML;
return $HTML;
}


//========================================
// {html:form_addwork}
function form_not_addwork() {
$HTML = <<<HTML
	<div id="config_part">
	<noscript><h2>Добавить работу</h2></noscript>
	<br /><b>Нельзя добавлять работы в выбранную категорию</b>
	</div>
HTML;
return $HTML;
}


//========================================
// {html:navpage}
function navpage() {
$HTML = <<<HTML
	Страницы: {html:navpage_el}
HTML;
return $HTML;
}


//========================================
// {html:navpage_el} 
function navpage_el($cat_id, $page) {
$HTML = <<<HTML
	<b><a href="{THIS}?act=portfolio&part=work&category={$cat_id}&page={$page}" class="pages">{$page}</a></b>
HTML;
return $HTML;
}


// end class
}

?>