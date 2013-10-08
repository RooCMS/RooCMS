{* Шаблон отображения лент сайта *}

<h3>Ленты</h3>

<div class="row hidden-xs">
	<div class="col-md-12">
		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th width="3%">ID</th>
					<th width="10%">Alias</th>
					<th width="47%">Название</th>
					<th width="10%" class="text-center">Тип</th>
					<th width="30%">Опции</th>
				</tr>
			</thead>
			<tbody>
    			{foreach from=$data item=feed}
    				<tr>
        				<td>{$feed['id']}</td>
        				<td>{$feed['alias']}</td>
        				<td>
        					<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}">{$feed['title']}</a> <small class="label label-info">{$feed['items']} эл.</small>
            				{if $feed['noindex'] == 1}<span class="text-muted"><sup>noindex</sup></span>{/if}
        				</td>
        				<td class="text-center"><span class="label label-default">{$feed['ptype']}</span></td>
        				<td>
        					<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}" class="btn btn-xs btn-default"><span class="icon-book icon-fixed-width"></span>Управление</a></nobr>
							&nbsp;<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$feed['id']}" class="btn btn-xs btn-default"><span class="icon-cog icon-fixed-width"></span>Настройки</a></nobr>
							{if $feed['id'] != 1}&nbsp;<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$feed['id']}" class="btn btn-xs btn-danger"><span class="icon-trash icon-fixed-width"></span>Удалить</a></nobr>{/if}
        				</td>
			        </tr>
    			{/foreach}
			</tbody>
		</table>
	</div>
</div>

{foreach from=$data item=feed}
<div class="panel panel-default visible-xs">
    <div class="panel-heading">
        <span class="label label-primary panel-title">{$feed['id']}</span>
		<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}" class="panel-title">{$feed['title']}</a>
    </div>
	<div class="panel-body">
		{if $feed['noindex'] == 1}<span class="text-warning">Неиндексируется в поиске</span>
        {else}<span class="text-info">Индексируется в поиске</span>
		{/if}
		<span class="text-muted"><br />в ленте {$feed['items']} элементов</span>
        <span class="label label-default pull-right">{$feed['ptype']}</span>
	</div>
	<div class="panel-footer text-right">
		<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$feed['id']}" class="btn btn-xs btn-default"><span class="icon-book icon-fixed-width"></span>Управление</a></nobr>
		<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=settings&page={$feed['id']}" class="btn btn-xs btn-default"><span class="icon-cog icon-fixed-width"></span>Настройки</a></nobr>
		{if $feed['id'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$feed['id']}" class="btn btn-xs btn-danger"><span class="icon-trash icon-fixed-width"></span>Удалить</a></nobr>{/if}
	</div>
</div>
{/foreach}