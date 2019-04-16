{* Template view mailing letter *}
<div class="card-header">
	{$letter['title']}
</div>
<div class="card-body">
	<div class=" card-text border border-2 p-3 mb-3">
		{$letter['message']}
	</div>
	<h6 class="card-title">Получатели:</h6>
	{foreach from=$recipients item=recipient}
		<a href="{$SCRIPT_NAME}?act=users&act=edit_user&uid={$recipient['uid']}" class="btn btn-outline-primary btn-sm">{$recipient['nickname']}<br /><span class="badge badge-light">{if $recipient['email'] != $recipient['actual_email']}<del class="text-gray">{/if}{$recipient['email']}{if $recipient['email'] != $recipient['actual_email']}</del>{/if}</span></a>
	{/foreach}

</div>
<div class="card-footer">
	<div class="row">
		<div class="col-12">
			<a href="{$SCRIPT_NAME}?act=mailing&part=archive_list" class="btn btn-secondary"><i class="fas fa-fw fa-arrow-left"></i> Вернуться в архив</a>
		</div>
	</div>
</div>

