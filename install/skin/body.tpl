
<form method="post" action="{if $step != $steps}{$SCRIPT_NAME}?step={$step}{else}{$smarty.const.CP}{/if}" role="form">
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Этап {$step} : {$page_title}</h3>
		<div class="small text-muted">{if trim($status) != ""}{$status}{/if}</div>
	</div>
		{if isset($noticetext) && trim($noticetext != "")}
			<div class="panel-body">
				{$noticetext}
			</div>
		{/if}
		{if !empty($log)}
		<table class="table">
			<tbody>
				{foreach from=$log item=str}
				<tr>
					<td class="text-right">
						{$str[0]}
					</td>
					<td class="text-{if $str[2]}success{else}danger{/if}">
						{$str[1]}
					</td>
					<td class="text-muted">
						{$str[3]}
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
		{/if}
	<div class="panel-footer text-right">
		{if isset($allowed) && $allowed}
			<input type="hidden" name="step" value="{$step}" readonly>
			<input type="submit" name="submit" class="btn btn-success" value="{if $step != $steps}Продолжить &rarr;{else}Завершить установку и перейти в панель управления RooCMS{/if}">
		{/if}
	</div>
</div>
</form>
