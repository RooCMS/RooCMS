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

	<ul class="list-group d-block d-sm-none">
		{foreach from=$data item=block}
			<li class="list-group-item no-overflow">

				<span class="badge badge-primary text-uppercase">{$block['block_type']}</span> <a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}">{$block['title']}</a>

				<div class="float-right">
					<div class="btn-group">
						<a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="btn btn-sm btn-outline-primary"><span class="far fa-edit fa-fw"></span></a>
						<a href="{$SCRIPT_NAME}?act=blocks&part=delete&block={$block['id']}" class="btn btn-sm btn-danger"><span class="far fa-trash-alt fa-fw"></span></a>
					</div>
				</div>
			</li>
		{/foreach}
	</ul>

{/if}
