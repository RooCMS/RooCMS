<?php

class tpl_items_acp_config {

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
			<li class="part">Компоненты</li>
			{html:parts_component}
			<li class="part">Моды</li>
			{html:parts_mod}
			<li class="part">Модули</li>
			{html:parts_module}
			</ul>
		</td>
		<td align="left" valign="top">
			<form method="post" action="{THIS}?act=config">
				{html:content}
			</form>
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



CSS;
return $CSS;
}

//****************************************************
// JS
function tpl_js() {
$JS = <<<JS
<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$(".tabs").tabs({ collapsible: false });
		$(".tabs ul").css("display","block");
	});
</script>
JS;
return $JS;
}

//#####################################################
//#		Элементы шаблона
//#####################################################

//=====================================
//	{html:content}
function content($part) {
$HTML = <<<HTML
<div class="tabs">
	<ul>
		<li><a href="#{$part['part']}">{$part['title']}</a></li>
	</ul>
	<div id="{$part['part']}">
		{$part['options']}
		<div id="option" align="right"><input type="submit" name="update_config" class="button" value="Сохранить настройки"></div>
	</div>
</div>
HTML;
return $HTML;
}

//=====================================
//	{html:content}
function options($option) {
$HTML = <<<HTML

	<div id="option">
		<table width="99%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="55%" align="left" valign="top">
					<font class="option_title">{$option['title']}</font>
					<br /><font class="rem">{$option['description']}</font>
				</td>
				<td width="45%" align="right" valign="top">
					{$option['option']}
				</td>
			</tr>
		</table>
	</div>
	
HTML;
return $HTML;
}


//===============================================
//	{html:parts}
function parts($parts, $select) {
$sel = "";
if($select == $parts['part']) $sel = ' class="sel"';
$HTML = <<<HTML
	<li{$sel}><a href="{THIS}?act=config&part={$parts['part']}">{$parts['title']}</a></li>
HTML;
return $HTML;
return $HTML;
}

/**************************************
* 	Fields for options type
**************************************/
function field_boolean($option, $value) {	// boolean
$true = ""; $false = "";
($value == "true") ? $true = " selected" : $false = " selected" ;
$HTML = <<<HTML
	<select name="{$option}" class="inp_s">
		<option value="true"{$true} style="background-color: #eeffee; color: #003300;">Да / Yes</option>
		<option value="false"{$false} style="background-color: #ffeeee; color: #330000;">Нет / No</option>
	</select>
HTML;
return $HTML;
}

function field_string($option, $value) { 	// string, int, email
$HTML = <<<HTML
	<input type="text" name="{$option}" class="f_input" value="{$value}">
HTML;
return $HTML;
}

function field_textarea($option, $value) {	// textarea
$HTML = <<<HTML
	<textarea name="{$option}" id="{$option}" class="f_textarea">{$value}</textarea>
	<br />
	<font class="ta_resize" id="p{$option}">+ увеличить</font> 
	<font class="ta_resize" id="m{$option}">- уменьшить</font>
	
	<script>
		$("#p{$option}").click(function(){
			$("#{$option}").animate({width: "+=150px", height: "+=250px"},350);
		});
		$("#m{$option}").click(function(){
			$("#{$option}").animate({width: "-=150px", height: "-=250px"},350);
		});
	</script>
HTML;
return $HTML;	
}

function field_date($option, $value) { 		// date
$HTML = <<<HTML
	<input type="text" name="{$option}" class="f_input" value="{$value}">
	<br />Дата вводится в формате: мм/дд/гггг
HTML;
return $HTML;
}

function field_select($option, $value) { 	// select
$HTML = <<<HTML
	<select name="{$option}" class="f_input">
		{$value}
	</select>
HTML;
return $HTML;
}

function field_select_option($option, $value, $selected="") { 		// select options
$HTML = <<<HTML
	<option value="{$value}" {$selected}>{$option}</option>
HTML;
return $HTML;
}

// end class
}

?>