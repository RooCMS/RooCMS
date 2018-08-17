{* Шаблон списка блоков *}
<div class="panel-heading">
	Блоки
</div>

{if empty($data)}
	<div class="panel-body">
		<p class="lead">Воспользуйтесь ссылкой слева, что бы создать первый блок.</p>
	</div>
{else}
	<table class="table table-hover table-condensed hidden-xs">
		<thead>
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
				<td class="text-muted">{$block['id']}</td>
				<td>{$block['alias']}</td>
				<td><a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}">{$block['title']}</a></td>
				<td><span class="label label-primary upper">{$block['block_type']}</span></td>
				<td class="text-right">
					<div class="btn-group">
						<a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
						<a href="{$SCRIPT_NAME}?act=blocks&part=delete&block={$block['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span><span class="hidden-sm">Удалить</span></a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<ul class="list-group visible-xs">
		{foreach from=$data item=block}
			<li class="list-group-item no-overflow">

				<span class="label label-primary upper">{$block['block_type']}</span> <a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}">{$block['title']}</a>

				<div class="pull-right">
					<div class="btn-group">
						<a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span></a>
						<a href="{$SCRIPT_NAME}?act=blocks&part=delete&block={$block['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span></a>
					</div>
				</div>
			</li>
		{/foreach}
	</ul>

{/if}
