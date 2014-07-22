{* Шаблон управления лентой *}

<div class="panel-heading">
	Лента "{$feed['title']}"

	<p class="pull-right"><a href="/?page={$feed['alias']}" target="_blank" class="btn btn-default btn-xs">Открыть ленту <span class="fa fa-external-link fa-fw"></span></a></p>
</div>

{if empty($feedlist)}
	<div class="panel-body">
		<p class="lead">
			В данной ленте пока что нет элементов
			<br />Нажмите на ссылку "Добавить элемент", что бы внести в ленту первый элемент
		</p>
	</div>
{else}

	<table class="table table-hover table-condensed hidden-xs">
		<thead>
		<tr class="active">
			<th width="55%" style="padding-left: 30px;">Заголовок</th>
			<th width="10%">Дата публикации</th>
			<th width="10%">Дата посл.изменений</th>
			<th width="25%">Опции</th>
		</tr>
		</thead>
		<tbody>
		{foreach from=$feedlist item=item}
			<tr>
				<td>
					{if $item['publication_status'] != "hide"}<a href="{$SCRIPT_NAME}?act=feeds&part=status_{if $item['status'] == 0}on{else}off{/if}_item&page={$feed['id']}&item={$item['id']}">{/if}
					<span class="fa fa-fw fa-eye{if $item['publication_status'] == "hide" || $item['status'] == 0}-slash text-muted{else} text-primary{/if}"></span>{if $item['publication_status'] != "hide"}</a>{/if}
					<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}"{if $item['publication_status'] == "hide" || $item['status'] == 0} class="text-muted"{/if}>{if $item['status'] == 0}<s>{/if}{$item['title']}{if $item['status'] == 0}</s>{/if}</a>
				</td>
				<td class="small">c {$item['date_publications']}{if $item['date_end_publications'] != 0}<br />по {$item['date_end_publications']}{/if}</td>
				<td class="small">{$item['date_update']}</td>
				<td>
					<div class="btn-group">
						<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span>Редактировать</a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=migrate_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><span class="fa fa-random fa-fw" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Переместить публикацию"></span></a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span>Удалить</a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>


	{foreach from=$feedlist item=item}

		<div class="panel-heading visible-xs">
			<span class="fa fa-fw fa-eye{if $item['publication_status'] == "hide" || $item['status'] == 0}-slash text-muted{else} text-primary{/if}"></span>
			<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}"{if $item['publication_status'] == "hide" || $item['status'] == 0} class="text-muted"{/if}>{if $item['status'] == 0}<s>{/if}{$item['title']}{if $item['status'] == 0}</s>{/if}</a>
		</div>
		<div class="panel-body visible-xs">
			<small class="pull-right">Пуб: c {$item['date_publications']}{if $item['date_end_publications'] != 0} по {$item['date_end_publications']}{/if}
				<br />Ред: {$item['date_update']}</small>
		</div>
		<div class="panel-body text-right visible-xs">
			<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span>Редактировать</a></nobr>
			<a href="{$SCRIPT_NAME}?act=feeds&part=migrate_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><span class="fa fa-random fa-fw" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Переместить публикацию"></span></a>
			<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span>Удалить</a></nobr>
		</div>

	{/foreach}

{/if}