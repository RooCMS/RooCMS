<?php


class tpl_items_acp_menu {

//#####################################################
//#		Основной шаблон
//#####################################################

function tpl_page() {
$HTML = <<<HTML

<center>
	<div id="acp_menu">
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td align="left">
					<ul>
						{html:menu_items_left}
					</ul>
				</td>
				<td align="right">
					<ul>
						{html:menu_items_right}
					</ul>
				</td>
			</tr>
		</table>
	</div>
</center>

HTML;
return $HTML;
}

//*****************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS

/* acp menu */
#acp_menu {width: 100%;
	background: #EDE1CC;
	border-top: 2px solid #B8AD9A;border-bottom: 1px solid #B8AD9A;
	margin-bottom: 10px; padding-bottom: 3px;
	font-family: 'Calibri';font-size: 14px;color: #587E94;
	z-index: 100; text-align: left;
}

#acp_menu ul, #acp_menu li {list-style: none;display: inline; clear: both; padding: 0px; margin: 0px;}
#acp_menu li:hover {}

a.menu_item 		{font-family: Calibri, 'Trebuchet MS';font-size: 16px;color: #373737;text-decoration: none;}
a.menu_item:visited {font-family: Calibri, 'Trebuchet MS';font-size: 16px;color: #373737;text-decoration: none;} 
a.menu_item:actived {font-family: Calibri, 'Trebuchet MS';font-size: 16px;color: #373737;text-decoration: none;}
a.menu_item:hover 	{color: #DA7A2E; text-decoration: none;}

/* acp submenu */
#acp_submenu {padding-top: 0px;padding-bottom: 0px;padding-left: 0px;padding-right: 0px; margin: 0px 5px 0px 0px;
	font-family: 'Tahoma';font-size: 11px;color: #373737;}

#acp_submenu li.part {list-style: none;color: #373737;font-weight: bold;
	width: 260px;line-height: 20px;font-size: 12px;	padding: 2px; margin: 0px;}
	
#acp_submenu li.part:hover {background-color: transparent;color: #373737;}

#acp_submenu li {list-style: none;color: #373737;
	width: 260px; height: 20px;	padding: 0px 0px 0px 10px; margin: 0px;}
	
#acp_submenu li:hover {color: #373737;background: #EDE1CC url('../img/acp/submenu_arrow.png') no-repeat 100% 50%;}
#acp_submenu li.sel {color: #373737;background: #D5CAB7 url('../img/acp/submenu_arrow.png') no-repeat 100% 50%; }
#acp_submenu a {font-family: 'Tahoma';color: #373737;text-decoration: underline;padding: 2px;vertical-align: middle;line-height: 19px;}
#acp_submenu a:hover {color: #DA7A2E;text-decoration: underline;}

#acp_submenu a#edit, #acp_submenu a#close {display: none;}
#acp_submenu li:hover a#edit, #acp_submenu li:hover a#close {display: inline;}

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

//#####################################################
//#		Элементы шаблона
//#####################################################

//***************************
//	Отображение элементов меню
//	{html:menu_items}
function menu_items($href, $img, $txt, $target="_self") {
$HTML = <<<HTML

	<li>
		&nbsp;<a href="{$href}" class="menu_item" target="{$target}"><img src="{$img}" width="16" height="16" border="0" alt="{$txt}" class="img"> <u>{$txt}</u></a>&nbsp;
	</li>

HTML;
return $HTML;
}


// end class
}

?>