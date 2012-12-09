{* Значение PHP переменных *}
<table width="99%" border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td width="25%" align="left" valign="middle">
			<b>Значение</b>
		</td>
		<td width="25%" align="left" valign="middle">
			<b title="Действующее значение">Локальное значение</b>
		</td>
		<td width="25%" align="left" valign="middle">
			<b>Значение на сервере</b>
		</td>
		<td width="25%" align="left" valign="middle">
			<b>Доступ</b>
		</td>
	</tr>
	{foreach from=$inivars item=inival key=ininame}
		<tr class="option">
			<td width="25%" align="left" valign="middle">
				{$ininame}
			</td>
			<td width="25%" align="left" valign="middle">
				{if $inival['local_value'] != $inival['global_value']}<b><font style="color: #009900;">{/if}{$inival['local_value']}{if $inival['local_value'] != $inival['global_value']}</font></b>{/if}
			</td>
			<td width="25%" align="left" valign="middle">
				{$inival['global_value']}
			</td>
			<td width="25%" align="left" valign="middle">
				{if $inival['access'] == 1}
					Через пользовательские скрипты
				{elseif $inival['access'] == 2 || $inival['access'] == 6}
					<font color="#aaa">Через <b>.htaccess</b>, php.ini или httpd.conf</font>
				{elseif $inival['access'] == 4}
					<font color="#bbb">Через php.ini или httpd.conf</font>
				{elseif $inival['access'] == 7}
					<font color="#030">Полный</font>
				{else}
					{$inival['access']}
				{/if}
			</td>
		</tr>
	{/foreach}
</table>