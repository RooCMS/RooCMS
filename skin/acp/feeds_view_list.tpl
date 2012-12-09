<div id="tabs">
	<ul style="display: none;">
		<li><a href="#feeds">Ленты сайта</a></li>
	</ul>
	<div id="feeds">
    	<noscript><h2>Ленты сайта</h2></noscript>
		<table width="99%" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td width="3%" align="left" valign="top">
					<b>ID</b>
				</td>
				<td width="10%" align="left" valign="top">
					<b>Alias</b>
				</td>
				<td width="47%" align="left" valign="top">
					<b>Название</b>
				</td>
				<td width="10%" align="left" valign="top">
					<b>Тип</b>
				</td>
				<td width="30%" align="left" valign="top">
					<b>Опции</b>
				</td>
			</tr>
		{foreach from=$data item=feed}
			<tr class="option">
				<td width="3%" align="left" valign="top">
					<font style="vertical-align: middle;">{$feed['id']}</font>
				</td>
				<td width="10%" align="left" valign="top">
					<font style="vertical-align: middle;">{$feed['alias']}</font>
				</td>
				<td width="47%" align="left" valign="top">
					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}" class="opt">{$feed['title']}</a> <small class="grey vmiddle">{$feed['items']} эл.</small>
                    {if $feed['noindex'] == 1}<small class="grey vmiddle bold">noindex</small>{/if}
				</td>
				<td width="10%" align="left" valign="top">
					<font style="vertical-align: middle;">{$feed['ptype']}</font>
				</td>
				<td width="30%" align="left" valign="top">
					<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$feed['id']}" class="opt"><img src="{$SKIN}/img/ico_settings.png" width="16" height="16" border="0" alt="" class="iconlink">Настройки</a></nobr>
					<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}" class="opt"><img src="{$SKIN}/img/ico_captcontrol.png" width="16" height="16" border="0" alt="" class="iconlink">Управление</a></nobr>
					{if $feed['id'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$feed['id']}" class="optat"><img src="{$SKIN}/img/ico_page_delete.png" width="16" height="16" border="0" alt="" class="iconlink">Удалить</a></nobr>{/if}
				</td>
			</tr>
		{/foreach}
		</table>
	</div>
</div>
