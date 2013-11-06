{* Шаблон отображения страниц сайта *}

<h3>Страницы</h3>

<div class="row hidden-xs">
	<div class="col-md-12">
		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th width="3%">ID</th>
					<th width="40%">Название <small>alias</small></th>
					<th width="11%" class="text-center">Тип</th>
					<th width="10%">Дата посл. редактирования</th>
					<th width="36%">Опции</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$data item=page}
    			<tr>
        			<td>{$page['sid']}</td>
        			<td>
        				<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}">{$page['title']}</a>
					<small>{$page['alias']}</small>
					{if $page['noindex'] == 1}<sup><span class="label label-default">noindex</span></sup>{/if}
        			</td>
        			<td class="text-center"><span class="label label-default">{$page['ptype']}</span></td>
        			<td class="small">{$page['lm']}</td>
        			<td>
					<nobr><a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span>Редактировать</a></nobr>
					{if $page['sid'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['sid']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span>Удалить</a></nobr>{/if}
        			</td>
    			</tr>
    			{/foreach}
			</tbody>
		</table>
	</div>
</div>

{foreach from=$data item=page}
<div class="panel panel-default visible-xs">
    <div class="panel-heading">
        <span class="label label-primary panel-title">{$page['sid']}</span>
		{if $page['page_type'] == "html" or $page['page_type'] == "php"}
			<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}" class="panel-title">{$page['title']}</a>
		{else}
			<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['sid']}" class="panel-title">{$page['title']}</a>
		{/if}
    </div>
	<div class="panel-body">
		{if $page['noindex'] == 1}<span class="text-warning">Неиндексируется в поиске</span>
        {else}<span class="text-info">Индексируется в поиске</span>
		{/if}
        <span class="label label-default pull-right">{$page['ptype']}</span>
	</div>
	<div class="panel-footer text-right">
		<nobr><a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['sid']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span> Редактировать</a></nobr>
		{if $page['sid'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['sid']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span> Удалить</a></nobr>{/if}
	</div>
</div>
{/foreach}