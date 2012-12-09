{literal}
<style>
/* logo */
#acp_logo {
	background-color: transparent;
	background-image: url('{/literal}{$SKIN}{literal}/img/bg_acp_logo.png');
	position: fixed;
	top: 0px; left: 0px;
	width: 280px; height: 85px;
	border: 0px; margin: 3px 0px 0px 0px;
	font-family: 'Ubuntu', 'Calibri','Trebuchet MS';font-size: 12px;
	text-shadow: #eeeeea 1px 1px 0px;
}
#acp_logo .logo {margin-top: 2px;margin-left: 3px;margin-right: 5px;float: left;border: 0px;}


/* acp menu */
#acp_menu {width: 100%; height: 28px; z-index: 99; text-align: left; position: fixed;top: 0px;
	background: #EDE1CC url('{/literal}{$SKIN}{literal}/img/bg_lightborder.gif') repeat-x bottom;
	border-top: 3px solid #EDE1CC;border-bottom: 1px solid #B8AD9A;
	margin: 0px 0px 20px 0px; padding: 3px 0px 3px 0px;
	font-family: 'Ubuntu', 'Calibri';font-size: 14px;color: #587E94;
	-moz-box-shadow: 0px -1px 4px #B8AD9A; -webkit-box-shadow: 0px -1px 4px #B8AD9A; box-shadow: 0px -1px 4px #B8AD9A;
}

#acp_menu ul, #acp_menu li { clear: both; display: inline; list-style: none; margin: 0px; padding: 0px; }

a.menu_item, a.menu_item:link, a.menu_item:visited 		{font-family: Ubuntu, 'Trebuchet MS';font-size: 13px;color: #373737;text-decoration: underline; padding: 10px 5px 4px 4px;}
a.menu_item:focus, a.menu_item:hover, a.menu_item:active {color: #DA7A2E; text-decoration: none;background: transparent url('{/literal}{$SKIN}{literal}/img/bg_menu_item.png') no-repeat 60% 70%;}

.menu_ico {padding-right: 4px;}

/* acp submenu */
#acp_submenu {padding: 0px 0px 0px 0px; margin: 30px 5px 0px 0px; font-family: 'Tahoma';font-size: 11px;color: #373737;position: fixed;}
#acp_submenu li.sel {color: #373737;background: #ede1cc;}
#acp_submenu li.part {list-style: none;color: #373737;font-weight: bold; width: 260px;line-height: 20px;font-size: 12px; padding: 2px; margin: 0px;}
#acp_submenu li.part:hover {background-color: transparent;color: #373737;}
#acp_submenu li {list-style: none;color: #373737; width: 260px; height: 20px; padding: 0px 0px 0px 10px; margin: 0px;}
#acp_submenu a {font-family: 'Tahoma';color: #373737;text-decoration: underline;padding: 2px;vertical-align: middle;line-height: 19px;}
#acp_submenu a:hover {color: #DA7A2E;text-decoration: underline;}
#acp_submenu a#edit, #acp_submenu a#close {display: none;}
#acp_submenu li:hover a#edit, #acp_submenu li:hover a#close {display: inline;}

</style>
{/literal}

<center>
	<div id="acp_menu">
		<table width="100%" border="0">
			<tr>
				<td width="280" align="left" valign="middle">
					<div id="acp_logo">
						<a href="{$SCRIPT_NAME}"><img src="{$SKIN}/img/acp_logo.png" class="logo"></a>
						ПАНЕЛЬ УПРАВЛЕНИЯ САЙТОМ
						<font class="rem">
							<br />ip: {$userip}
						</font>
					</div>
				</td>
				<td align="left">
					<ul>
						{foreach from=$menu_items_left item=menu_item}
						<li>
							&nbsp;<a href="{$menu_item['link']}" class="menu_item" target="{$menu_item['window']}"><img src="{$menu_item['icon']}" width="16" height="16" border="0" alt="{$menu_item['text']}" class="img menu_ico">{$menu_item['text']}</a>&nbsp;
						</li>
						{/foreach}
					</ul>
				</td>
				<td align="right">
					<ul>
						{foreach from=$menu_items_right item=menu_item}
						<li>
							&nbsp;<a href="{$menu_item['link']}" class="menu_item" target="{$menu_item['window']}"><img src="{$menu_item['icon']}" width="16" height="16" border="0" alt="{$menu_item['text']}" class="img menu_ico">{$menu_item['text']}</a>&nbsp;
						</li>
						{/foreach}
					</ul>
				</td>
			</tr>
		</table>
	</div>
</center>