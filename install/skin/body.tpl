<center>
	<table width="99%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="270" align="left" valign="top">
			</td>
			<td align="left" valign="top">
				<div id="tabs">
					<ul style="display: none;">
						<li><a href="#log">Этап {$step} : {$page_title}</a></li>
					</ul>
					<div id="log">
						<form method="post" action="{if $step != $steps}{$SCRIPT_NAME}?step={$step}{else}{$smarty.const.CP}{/if}">
							<table width="99%" border="0" cellpadding="4" cellspacing="0">
								{if isset($noticetext) && trim($noticetext != "")}
									{$noticetext}
								{/if}
								{foreach from=$log item=str}
									<tr class="option">
										<td width="33%" valign="middle">
											<nobr><b>{$str[0]}</b></nobr>
										</td>
										<td width="34%" valign="middle">
											<nobr><b>
											{if $str[2]}
												<font color="green">
											{else}
												<font color="red">
											{/if}
											{$str[1]}
											</font>
											</b></nobr>
										</td>
										<td width="33%" valign="middle">
											<font class="dgrey">{$str[3]}</font>
										</td>
									</tr>
								{/foreach}
							</table>
							{if isset($allowed) && $allowed}
								<div style="text-align: right;width: 99%;">
									<input type="hidden" name="empty" value="1">
									<input type="hidden" name="step" value="{$step}">
									<!-- <font class="grey" rel="timer.30"></font> --><input type="submit" name="submit" class="f_submit" value="{if $step != $steps}Продолжить &rarr;{else}Завершить установку и перейти в панель управления RooCMS{/if}">
								</div>
							{/if}
						</form>
					</div>
				</div>
			</td>
		</tr>
	</table>
</center>