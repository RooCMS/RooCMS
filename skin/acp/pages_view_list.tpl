{* Шаблон отображения страниц сайта *}
<div class="panel-heading">
	Страницы
</div>


<table class="table table-hover table-condensed hidden-xs">
	<thead>
		<tr class="active">
			<th width="3%">ID</th>
			<th width="46%">Название <small>alias</small></th>
			<th width="11%">Тип</th>
			<th width="10%">Дата редактирования</th>
			<th width="30%">Опции</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$data item=page}
		<tr>
			<td class="text-muted">{$page['sid']}</td>
			<td>
				<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}">{$page['title']}</a>
				{if $page['noindex'] == 1}<sup><i class="fa fa-fw fa-info mark" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Неиндексируется поисковыми системами"></i></sup>{/if}
				<small class="alias-vis">{$page['alias']}</small>
			</td>
			<td class="text-left"><span class="label label-default">{$page['ptype']}</span></td>
			<td class="small">{$page['lm']}</td>
			<td>
				<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
					{if $page['sid'] != 1}<a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['sid']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span><span class="hidden-sm">Удалить</span></a>{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>


<ul class="list-group visible-xs">
	{foreach from=$data item=page}
		<li class="list-group-item">

			{if $page['page_type'] == "html" or $page['page_type'] == "php"}
				<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}">{$page['title']}</a>
			{else}
				<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['sid']}">{$page['title']}</a>
			{/if}

			<div class="pull-right">
				<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span></a>
					{if $page['sid'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['sid']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span></a></nobr>{/if}
				</div>
			</div>
		</li>
	{/foreach}
</ul>