{* Шаблон отображения лент сайта *}

<div class="panel-heading">
	Ленты
</div>

<table class="table table-hover table-condensed hidden-xs">
	<thead>
		<tr class="active">
			<th width="3%">ID</th>
			<th width="46%">Название <small>alias</small></th>
			<th width="11%"></th>
			<th width="10%" class="text-center">Тип</th>
			<th width="30%">Опции</th>
		</tr>
	</thead>
	<tbody>
	{if !empty($data)}
		{foreach from=$data item=feed}
			<tr>
				<td class="text-muted">{$feed['id']}</td>
				<td>
					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}">{$feed['title']}</a>
					{if $feed['noindex'] == 1}<sup><i class="fa fa-fw fa-info mark" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Неиндексируется поисковыми системами"></i></sup>{/if}
					<small class="alias-vis">{$feed['alias']}</small>
				</td>
				<td class="text-right">
					<small>{$feed['items']} элемент{if $feed['items']!=1 && $feed['items'] > 4}ов{/if}{if $feed['items'] >= 2 && $feed['items'] <= 4}а{/if}</small>
				</td>
				<td class="text-center"><span class="label label-default">{$feed['ptype']}</span></td>
				<td>
					<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}" class="btn btn-xs btn-default"><span class="fa fa-book fa-fw"></span><span class="hidden-sm hidden-md">Управление</span></a>
					<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$feed['id']}" class="btn btn-xs btn-default"><span class="fa fa-cog fa-fw"></span><span class="hidden-sm hidden-md">Настройки</span></a>
					{if $feed['id'] != 1}<a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$feed['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span><span class="hidden-sm hidden-md">Удалить</span></a>{/if}
					</div>
				</td>
			</tr>
		{/foreach}
	{else}
	<tr class="warning">
		<td colspan="5">
			Вы не создали ни одной ленты. Воспользуйтесь меню слева.
		</td>
	</tr>
	{/if}
	</tbody>
</table>

{if !empty($data)}
<ul class="list-group visible-xs">
	{foreach from=$data item=feed}
		<li class="list-group-item">

			<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}">{$feed['title']}</a>

			<div class="pull-right">
				<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}" class="btn btn-xs btn-default"><span class="fa fa-book fa-fw"></span></a>
					<a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$feed['id']}" class="btn btn-xs btn-default"><span class="fa fa-cog fa-fw"></span></a>
					{if $feed['id'] != 1}<a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$feed['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span></a>{/if}
				</div>
			</div>
		</li>
	{/foreach}
</ul>
{else}
	<div class="panel-body alert alert-warning visible-xs">
		Вы не создали ни одной ленты. Воспользуйтесь опцией "<a href="{$SCRIPT_NAME}?act=structure&part=create&type=feed">Создать новую ленту</a>".
	</div>
{/if}