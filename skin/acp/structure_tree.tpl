{* Шаблон отображения структуры сайта *}
<div class="panel-heading">
	Структура сайта
</div>

<table class="table table-hover table-condensed hidden-xs">
	<thead>
		<tr class="active">
			<th width="3%">ID</th>
			<th width="56%">Название <small>alias</small></th>
			<th width="11%" class="text-center">Тип</th>
			<th width="30%" class="text-right">Опции</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$tree item=page}
		<tr>
			<td class="text-muted structure-highlight text-right">{$page['id']}<i class="fa fa-fw fa-caret-right"></i></td>
			<td>
				<nobr>
					{section name=foo start=1 loop=$page['level'] step=1}<span class="text-muted structure-highlight">&bull;</span>&emsp;{/section}

					{if $page['page_type'] == "html" or $page['page_type'] == "php"}
						<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['id']}">{$page['title']}</a>
					{else}
						<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['id']}">{$page['title']}</a>
					{/if}
				</nobr>
				{if $page['noindex'] == 1}<sup><i class="fa fa-fw fa-info mark" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Неиндексируется поисковыми системами"></i></sup>{/if}
				<small class="trinfo">{$page['alias']}</small>
			</td>
			<td class="text-left">
				<span class="label label-default">{$page_types[$page['page_type']]}</span>
				{if $page['page_type'] == "feed"}
				<span class="label label-info">{$page['items']} эл.</span>
				{/if}
			</td>
			<td class="text-right">
				<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span><span class="hidden-sm">Редактировать</span></a>
					{if $page['id'] != 1}<a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span><span class="hidden-sm">Удалить</span></a>{/if}
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>

<ul class="list-group visible-xs">
	{foreach from=$tree item=page}
		<li class="list-group-item">

			{section name=foo start=1 loop=$page['level'] step=1}
				<span class="text-muted">&emsp;</span>
			{/section}

			{*<span class="text-muted">{$page['id']}</span>*}

			{if $page['page_type'] == "html" or $page['page_type'] == "php"}
				<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['id']}">{$page['title']}</a>
			{else}
				<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['id']}">{$page['title']}</a>
			{/if}

			<div class="pull-right">
				<div class="btn-group">
					<a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span></a>
					{if $page['id'] != 1}<a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span></a>{/if}
				</div>
			</div>
		</li>
	{/foreach}
</ul>