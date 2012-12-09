<div id="tabs">
	<ul style="display: none;">
		<li><a href="#feed">Лента "{$feed['title']}"</a></li>
	</ul>
	<div id="feed">
		<noscript><h2>Лента "{$feed['title']}"</h2></noscript>
		{if empty($feedlist)}В данной ленте пока что нет элементов
		<br />Нажмите на ссылку "Добавить элемент", что бы внести в ленту первый элемент
		{else}

		<table width="99%" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td width="50%" align="left" valign="middle">
					<b>Заголовок</b>
				</td>
				<td width="10%" align="left" valign="middle">
					<b>Дата публикации</b>
				</td>
				<td width="10%" align="left" valign="middle">
					<b>Дата посл.изменений</b>
				</td>
				<td width="30%" align="left" valign="middle">
					<b>Опции</b>
				</td>
			</tr>
			{foreach from=$feedlist item=item}
				<tr class="option">
					<td width="50%" align="left" valign="middle" class="overflowh">
						<!-- <nobr> --><a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="opt" title="{$item['title']}">{$item['title']}</a><!-- </nobr> -->
					</td>
					<td width="10%" align="left" valign="middle">
						{$item['date_publications']}
					</td>
					<td width="10%" align="left" valign="middle">
						{$item['date_update']}
					</td>
					<td width="30%" align="left" valign="middle">
						<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="opt"><img src="{$SKIN}/img/ico_item_edit.png" width="16" height="16" border="0" alt="" class="iconlink">Редактировать</a></nobr>
						<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="optat"><img src="{$SKIN}/img/ico_item_delete.png" width="16" height="16" border="0" alt="" class="iconlink">Удалить</a></nobr>
					</td>
				</tr>

			{/foreach}
		</table>
		{/if}
	</div>
</div>
