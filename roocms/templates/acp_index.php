<?php


class tpl_items_acp_index {

//#####################################################
//#		Основной шаблон
//#####################################################

function tpl_page() {
$HTML = <<<HTML
<center>
	<table width="99%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left" valign="top">
				<div id="content">
				<div id="tabs">
					<ul style="display: none;">
						<li><a href="#serverinfo">Информация о сервере</a></li>
						<li><a href="#filetypes">Допустимые форматы и размеры</a></li>
					</ul>
					<div id="serverinfo">
					{html:version}
					{html:processor}
					</div>
					<div id="filetypes">
					{html:filetypes}
					</div>
				</div>
			</td>
		</tr>
	</table>
</center>
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
//	{html:version} 
function version($version) {
$HTML = <<<HTML

	<div id="config_part">
	<noscript><h2>Информация о сервере (software)</h2></noscript>
	<div id="option">
		<b>Версия PHP:</b> 							
			{$version['php']}
	</div>
	<div id="option">
		<b>Версия MySQL:</b>					
			{$version['mysql']}
	</div>
	<div id="option">
		<b>Apache:</b> 						
			{$version['apache']}
	</div>
	<div id="option">
		<b>Операционная система:</b> 			
			{$version['os']}
	</div>
	<div id="option">
		<b>Лимит памяти:</b> 			
			{$version['ml']}
	</div>
	<div id="option">
		<b>Максимальный размер файлов для загрузки:</b> 	
			{$version['mfs']}	
	</div>
	<div id="option">
		<b>Максимальный размер постинга:</b> 	
			{$version['mps']}
	</div>
	<div id="option">
		<b>Максимальное время исполнения скрипта:</b> 	
			{$version['met']} секунд
	</div>	
	</div>

HTML;
return $HTML;
}


//***************************
//	{html:filetypes} 
function filetypes($filetypes) {
$HTML = <<<HTML

	<div id="config_part">
	<noscript><div id="part_title">Допустимые форматы и размеры</div></noscript>
	<div id="option">
		<b>Максимальный размер файлов для загрузки:</b> 	
			{$filetypes['mfs']}	
	</div>
	<div id="option">
		<b>Разрешенные к загрузке форматы изображений:</b> 	
			{$filetypes['images']}	
	</div>
	<div id="option">
		<b>Разрешенные к загрузке форматы файлов:</b> 	
			{$filetypes['files']}	
	</div>
	</div>

HTML;
return $HTML;
}

//***************************
//	output file extension
function ext($type) {
$HTML = <<<HTML
	<img src="/img/icon/16/{$type['ico']}" border="0" width="16" height="16" alt="{$type['type']}" class="img"> {$type['ext']} &nbsp;&nbsp;
HTML;
return $HTML;
}

// end class
}
?>