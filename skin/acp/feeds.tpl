<center>
	<table width="99%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td width="270" align="left" valign="top">
			<ul id="acp_submenu">
				<li class="part">Действия</li>
					<li><img src="{$SKIN}/img/ico_page_create.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?act=structure&part=create&type=feed">Создать новую ленту</a></li>
					{if isset($smarty.get.part) && ($smarty.get.part == 'control' || $smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
						<li{if isset($smarty.get.part) && $smarty.get.part == "create_item"} class="sel"{/if}><img src="{$SKIN}/img/ico_item_create.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?act=feeds&part=create_item&page={$smarty.get.page}">Создать новый элемент ленты</a></li>
						<li{if isset($smarty.get.part) && $smarty.get.part == "settings"} class="sel"{/if}><img src="{$SKIN}/img/ico_settings.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$smarty.get.page}">Настройки ленты</a></li>
					{/if}
				{if isset($smarty.get.part) && ($smarty.get.part == 'edit_item' || $smarty.get.part == 'settings' || $smarty.get.part == 'create_item')}
					<li class="part">Опции</li>
						<li><img src="{$SKIN}/img/ico_back.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$smarty.get.page}">Вернуться в ленту</a></li>
				{/if}
			</ul>
		</td>
		<td align="left" valign="top">
			{$content}
		</td>
		</tr>
	</table>
</center>