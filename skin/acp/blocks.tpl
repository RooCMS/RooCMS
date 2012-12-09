<center>
	<table width="99%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td width="270" align="left" valign="top">
			<ul id="acp_submenu">
			<li class="part">Действия</li>
				<li{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "html"} class="sel"{/if}><img src="{$SKIN}/img/ico_createblock.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?act=blocks&part=create&type=html">Создать новый HTML блок</a></li>
				<li{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "php"} class="sel"{/if}><img src="{$SKIN}/img/ico_createblock.png" width="16" height="16" border="0" alt="" class="img"> <a href="{$SCRIPT_NAME}?act=blocks&part=create&type=php">Создать новый PHP блок</a></li>
			</ul>
		</td>
		<td align="left" valign="top">
			{$content}
		</td>
		</tr>
	</table>
</center>