<div id="tabs">
	<ul style="display: none;">
		<li><a href="#pages">Страницы сайта</a></li>
	</ul>
	<div id="pages">
		<noscript><h2>Страницы сайта</h2></noscript>
		<table width="99%" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td width="3%" align="left" valign="middle">
					<b>ID</b>
				</td>
				<td width="10%" align="left" valign="middle">
					<b>Alias</b>
				</td>
				<td width="37%" align="left" valign="middle">
					<b>Название</b>
				</td>
				<td width="10%" align="left" valign="middle">
					<b>Тип</b>
				</td>
				<td width="10%" align="left" valign="middle">
					<b>Дата посл. редактирования</b>
				</td>
				<td width="30%" align="left" valign="middle">
					<b>Опции</b>
				</td>
			</tr>
		{foreach from=$data item=page}
			<tr class="option">
				<td width="3%" align="left" valign="middle">
					<font style="vertical-align: middle;">{$page['sid']}</font>
				</td>
				<td width="10%" align="left" valign="middle">
					<font style="vertical-align: middle;">{$page['alias']}</font>
				</td>
				<td width="37%" align="left" valign="middle">
					<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}" class="opt">{$page['title']}</a>
                    {if $page['noindex'] == 1}<small class="grey vmiddle bold">noindex</small>{/if}
				</td>
				<td width="10%" align="left" valign="middle">
					<nobr><font style="vertical-align: middle;">{$page['ptype']}</font></nobr>
				</td>
				<td width="10%" align="left" valign="middle">
					<nobr><font style="vertical-align: middle;">{$page['lm']}</font></nobr>
				</td>
				<td width="30%" align="left" valign="middle">
					<nobr><a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}" class="opt"><img src="{$SKIN}/img/ico_page_edit.png" width="16" height="16" border="0" alt="" class="iconlink">Редактировать</a></nobr>
					{if $page['sid'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['sid']}" class="optat"><img src="{$SKIN}/img/ico_page_delete.png" width="16" height="16" border="0" alt="" class="iconlink">Удалить</a></nobr>{/if}
				</td>
			</tr>
		{/foreach}
		</table>
	</div>
</div>
