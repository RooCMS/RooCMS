<div id="tabs">
	<ul style="display: none;">
		<li><a href="#blocks">Блоки сайта</a></li>
	</ul>
	<div id="blocks">
		<noscript><h2>Блоки сайта</h2></noscript>
		{if !empty($data)}
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
			{foreach from=$data item=block}
				<tr class="option">
					<td width="3%" align="left" valign="top">
						<font style="vertical-align: middle;">{$block['id']}</font>
					</td>
					<td width="10%" align="left" valign="top">
						<font style="vertical-align: middle;">{$block['alias']}</font>
					</td>
					<td width="47$" align="left" valign="top">
						<a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="opt">{$block['title']}</a>
					</td>
					<td width="10%" align="left" valign="top">
						<font class="upper" style="vertical-align: middle;">{$block['type']}</font>
					</td>
					<td width="30%" align="left" valign="top">
						<nobr><a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="opt"><img src="{$SKIN}/img/ico_editblock.png" width="16" height="16" border="0" alt="" class="iconlink">Редактировать</a></nobr>
						<nobr><a href="{$SCRIPT_NAME}?act=blocks&part=delete&block={$block['id']}" class="opt"><img src="{$SKIN}/img/ico_delblock.png" width="16" height="16" border="0" alt="" class="iconlink">Удалить</a> </nobr>
					</td>
				</tr>
			{/foreach}
			</table>
		{else}
			Воспользуйтесь ссылкой слева, что бы создать первый блок.
		{/if}
	</div>
</div>
