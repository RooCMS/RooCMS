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
			<th width="30%">Опции</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$data item=block}
		<tr>
			<td class="text-muted">{$block['id']}</td>
			<td>{$block['alias']}</td>
			<td><a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}">{$block['title']}</a></td>
			<td><span class="label label-primary upper">{$block['block_type']}</span></td>
			<td>
				<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
					<a href="{$SCRIPT_NAME}?act=blocks&part=delete&block={$block['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span><span class="hidden-sm">Удалить</span></a>
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>

{foreach from=$data item=block}
	<div class="panel-body visible-xs">
		<span class="label label-primary pull-right upper">{$block['block_type']}</span>
		<a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}">{$block['title']}</a>
		<br /><a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="small text-muted">{$block['alias']}</a>
	</div>
	<div class="panel-footer text-right visible-xs">
		<nobr><a href="{$SCRIPT_NAME}?act=blocks&part=edit&block={$block['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span>Редактировать</a></nobr>
		<nobr><a href="{$SCRIPT_NAME}?act=blocks&part=delete&block={$block['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span>Удалить</a> </nobr>
	</div>
{/foreach}
{/if}
