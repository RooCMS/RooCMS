{* View list block template *}
<div class="card-header">
	Блоки
</div>

{if empty($data)}
	<div class="card-body">
		<p class="lead">Воспользуйтесь ссылкой слева, что бы создать первый блок.</p>
	</div>
{else}
	<table class="table table-hover d-none d-sm-table mb-0">
		<thead class="bg-light">
			<tr class="active">
				<th width="3%">ID</th>
				<th width="10%">Alias</th>
				<th width="47%">Название</th>
				<th width="10%">Тип</th>
				<th width="30%" class="text-right">Опции</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$data item=block}
			<tr>
				<td class="align-middle text-muted text-right small">{$block['id']}</td>
				<td class="align-middle">{$block['alias']}</td>
				<td class="align-middle"><a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}">{$block['title']}</a></td>
				<td class="align-middle"><span class="badge badge-primary text-uppercase">{$block['block_type']}</span></td>
				<td class="text-right align-middle">
					<div class="btn-group">
						<a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="btn btn-sm btn-outline-primary"><span class="far fa-edit fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
						<a href="{$SCRIPT_NAME}?act=blocks&part=delete&block={$block['id']}" class="btn btn-sm btn-danger"><span class="far fa-trash-alt fa-fw"></span><span class="hidden-sm">Удалить</span></a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<table class="table table-hover d-block-table d-sm-none mb-0">
		<tbody>
		{foreach from=$data item=block}
			<tr>
				<td class="align-middle">
					<a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}">{$block['title']}</a>
					<span class="badge badge-primary text-uppercase float-right">{$block['block_type']}</span>
				</td>
				<td class="w-25 align-middle text-right">
					<div class="btn-group btn-group-sm">
						<a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="btn btn-outline-primary"><span class="far fa-edit fa-fw"></span></a>
						<a href="{$SCRIPT_NAME}?act=blocks&part=delete&block={$block['id']}" class="btn btn-danger"><span class="far fa-trash-alt fa-fw"></span></a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{/if}
