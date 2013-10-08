{* Шаблон отображения страниц сайта *}

<h3>Страницы</h3>

<div class="row hidden-xs">
	<div class="col-md-12">
		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th width="3%">ID</th>
					<th width="10%">Alias</th>
					<th width="37%">Название</th>
					<th width="10%" class="text-center">Тип</th>
					<th width="10%">Дата посл. редактирования</th>
					<th width="30%">Опции</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$data item=page}
    			<tr>
        			<td>{$page['sid']}</td>
        			<td>{$page['alias']}</td>
        			<td>
        				<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}">{$page['title']}</a>
            			{if $page['noindex'] == 1}<span class="text-muted"><sup>noindex</sup></span>{/if}
        			</td>
        			<td class="text-center"><span class="label label-default">{$page['ptype']}</span></td>
        			<td class="small">{$page['lm']}</td>
        			<td>
						<nobr><a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['sid']}" class="btn btn-xs btn-default"><span class="icon-edit icon-fixed-width"></span>Редактировать</a></nobr>
						{if $page['sid'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['sid']}" class="btn btn-xs btn-danger"><span class="icon-trash icon-fixed-width"></span>Удалить</a></nobr>{/if}
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
		{if $page['type'] == "html" or $page['type'] == "php"}
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
		<nobr><a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['sid']}" class="btn btn-xs btn-default"><span class="icon-edit icon-fixed-width"></span> Редактировать</a></nobr>
		{if $page['sid'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['sid']}" class="btn btn-xs btn-danger"><span class="icon-trash icon-fixed-width"></span> Удалить</a></nobr>{/if}
	</div>
</div>
{/foreach}