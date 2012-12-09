<center>
	<table width="99%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td width="270" align="left" valign="top">
			<ul id="acp_submenu">
			<li class="part">Компоненты</li>
				{if !empty($parts['component'])}
				{foreach from=$parts['component'] item=part}
					<li{if $thispart == $part['name']} class="sel"{/if}>{if trim($part['ico']) != ""}<img src="{$SKIN}/img/{$part['ico']}" width="16" height="16" border="0" alt="{$part['title']}" title="{$part['title']}" class="img"> {/if}<a href="{$SCRIPT_NAME}?act=config&part={$part['name']}">{$part['title']}</a></li>
				{/foreach}
				{/if}
			<li class="part">Модули</li>
				{if !empty($parts['mod'])}
				{foreach from=$parts['mod'] item=part}
					<li{if $thispart == $part['name']} class="sel"{/if}>{if trim($part['ico']) != ""}<img src="{$SKIN}/img/{$part['ico']}" width="16" height="16" border="0" alt="{$part['title']}" title="{$part['title']}" class="img"> {/if}<a href="{$SCRIPT_NAME}?act=config&part={$part['name']}">{$part['title']}</a></li>
				{/foreach}
				{/if}
			<li class="part">Виджеты</li>
				{if !empty($parts['widget'])}
				{foreach from=$parts['widget'] item=part}
					<li{if $thispart == $part['name']} class="sel"{/if}>{if trim($part['ico']) != ""}<img src="{$SKIN}/img/{$part['ico']}" width="16" height="16" border="0" alt="{$part['title']}" title="{$part['title']}" class="img"> {/if}<a href="{$SCRIPT_NAME}act=config&part={$part['name']}">{$part['title']}</a></li>
				{/foreach}
				{/if}
			</ul>
		</td>
		<td align="left" valign="top">
			<form method="post" action="{$SCRIPT_NAME}?act=config">
				<div id="tabs">
					<ul style="display: none;">
						<li><a href="#{$this_part['name']}">{$this_part['title']}</a></li>
					</ul>
					<div id="{$this_part['name']}">
						<noscript><h2>{$this_part['title']}</h2></noscript>
						{foreach from=$this_part['options'] item=option}
							<div class="option" title="$config->{$option['option_name']}">
								<table width="99%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td width="44%" align="left" valign="top">
											<font class="option_title">{$option['title']}</font>
											<br /><font class="rem">{$option['description']}</font>
										</td>
										<td width="55%" align="right" valign="top">
											{$option['option']}
										</td>
										<!-- <td valign="top" style="padding-top: 5px;"></td>  -->
									</tr>
								</table>
							</div>
						{/foreach}
						<div id="option" style="text-align: right;width: 99%;">
							<input type="hidden" name="empty" value="1">
							<input type="submit" name="update_config" class="f_submit" value="Сохранить настройки"></div>
					</div>
				</div>
			</form>
		</td>
		</tr>
	</table>
</center>