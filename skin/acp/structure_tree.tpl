{* Шаблон отображения структуры сайта *}

<h3>Структура сайта</h3>

<div class="row hidden-xs">
	<div class="col-md-12">
		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th width="3%">ID</th>
					<th width="17%">Alias страницы</th>
					<th width="45%">Название страницы</th>
					<th width="9%" class="text-center">Тип страницы</th>
					<th width="26%">Опции</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$tree item=page}
				<tr>
    				<td class="text-muted">{$page['id']}</td>
    				<td>
    					<nobr>
    						{section name=foo start=1 loop=$page['level'] step=1}
                				<span class="text-muted">&middot;</span>
    						{/section}
    						{$page['alias']}
    					</nobr>
    				</td>
    				<td>
    					<nobr>
    						{section name=foo start=1 loop=$page['level'] step=1}
                				<span class="text-muted">&middot;</span>
    						{/section}

							{if $page['page_type'] == "html" or $page['page_type'] == "php"}
								<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['id']}">{$page['title']}</a>
							{else}
								<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['id']}">{$page['title']}</a> <small class="label label-info">{$page['items']} эл.</small>
							{/if}
						</nobr>
						{if $page['noindex'] == 1}<span class="text-muted"><sup>noindex</sup></span>{/if}
    				</td>
    				<td class="text-center"><span class="label label-default">{$page_types[$page['page_type']]}</span></td>
    				<td>
						<nobr><a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span>Редактировать</a></nobr>
						{if $page['id'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span>Удалить</a></nobr>{/if}
    				</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>


{foreach from=$tree item=page}
<div class="panel panel-default visible-xs">
    <div class="panel-heading">
        <span class="label label-primary panel-title">{$page['id']}</span>
		{if $page['page_type'] == "html" or $page['page_type'] == "php"}
			<a href="{$SCRIPT_NAME}?act=pages&part=edit&page={$page['id']}" class="panel-title">{$page['title']}</a>
		{else}
			<a href="{$SCRIPT_NAME}?act=feeds&part=control&page={$page['id']}" class="panel-title">{$page['title']}</a>
		{/if}
    </div>
	<div class="panel-body">
		{if $page['noindex'] == 1}<span class="text-warning">Неиндексируется в поиске</span>
        {else}<span class="text-info">Индексируется в поиске</span>
		{/if}
		{if $page['page_type'] != "html" and $page['page_type'] != "php"}
			<span class="text-muted"><br />в ленте {$page['items']} элементов</span>
		{/if}
        <span class="label label-default pull-right">{$page_types[$page['page_type']]}</span>
	</div>
	<div class="panel-footer text-right">
		<nobr><a href="{$SCRIPT_NAME}?act=structure&part=edit&id={$page['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span> Редактировать</a></nobr>
		{if $page['id'] != 1}<nobr><a href="{$SCRIPT_NAME}?act=structure&part=delete&id={$page['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span> Удалить</a></nobr>{/if}
	</div>
</div>
{/foreach}