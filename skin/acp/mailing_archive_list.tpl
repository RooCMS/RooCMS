{* Archive list messages *}
<div class="card-header">
	Архив сообщений
</div>

<table class="table table-hover table-condensed d-none d-sm-table mb-0">
	<thead class="bg-light">
	<tr class="active">
		<th width="2%">ID</th>
		<th width="21%">Автор</th>
		<th width="47%">Название</th>
		<th width="30%">Дата</th>
	</tr>
	</thead>
	<tbody>
	{foreach from=$list item=letter}
		<tr>
			<td class="align-middle text-muted">{$letter['id']}</td>
			<td class="align-middle">
				<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$letter['author_id']}" class="btn btn-sm btn-outline-secondary">{$letter['nickname']}</a>
			</td>
			<td class="align-middle text-truncate">
				<a href="{$SCRIPT_NAME}?act=mailing&part=archive_letter&id={$letter['id']}">{$letter['title']}</a>
			</td>
			<td class="align-middle">{$letter['date_create']}</td>
		</tr>
	{/foreach}
	</tbody>
</table>


<table class="table table-hover d-block-table d-sm-none mb-0">
	<tbody>
	{foreach from=$list item=letter}
		<tr>
			<td class="align-middle">
				<a href="{$SCRIPT_NAME}?act=mailing&part=archive_letter&id={$letter['id']}">{$letter['title']}</a>
				<small class="float-right">{$letter['date_create']}</small>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>