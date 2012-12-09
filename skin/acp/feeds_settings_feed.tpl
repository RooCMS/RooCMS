<div id="tabs">
	<ul style="display: none;">
		<li><a href="#feed">Настройки ленты</a></li>
	</ul>
	<div id="feed">
		<noscript><h2>Настройки ленты</h2></noscript>
		<form method="post" action="{$SCRIPT_NAME}?act=feeds&part=update_settings&page={$feed['id']}">
			<div class="option">
				<table width="99%" border="0" cellpadding="0" cellspacing="0">
					<tr class="buttonset">
						<td width="45%" align="left" valign="top">
							<font class="option_title">RSS вывод</font>
							<br /><font class="rem">Включить / Выключить вывод ленты в RSS формате</font>
						</td>
						<td width="55%" align="right" valign="top">
							<input type="radio" name="rss" value="1" id="flag_rss_on"{if $feed['rss'] == 1} checked{/if}><label for="flag_rss_on">Вкл</label>
							<input type="radio" name="rss" value="0" id="flag_rss_off"{if $feed['rss'] == 0} checked{/if}><label for="flag_rss_off">Выкл</label>
						</td>
					</tr>
				</table>
			</div>
			<div class="option">
				<table width="99%" border="0" cellpadding="0" cellspacing="0">
					<tr class="buttonset">
						<td width="45%" align="left" valign="top">
							<font class="option_title">Кол-во новостей на страницу</font>
							<br /><font class="rem">Укажите кол-во новостей выводимых на одной странице<br />По умолчанию: 10. При значении 0 будет использовано значение по умолчанию.</font>
						</td>
						<td width="55%" align="right" valign="top">
							<input type="text" name="items_per_page" class="f_input" value="{$feed['items_per_page']}">
						</td>
					</tr>
				</table>
			</div>
			<table width="99%" border="0" cellpadding="1" cellspacing="0">
				<tr>
					<td width="99%" colspan="2" align="right" valign="top">
						<input type="submit" class="f_submit" name="update_settings" value="Сохранить настройки">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
