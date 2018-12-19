{* Шаблон управления лентой *}

<div class="panel-heading">
	Лента "{$feed['title']}"

	<p class="pull-right"><a href="/?page={$feed['alias']}" target="_blank" class="btn btn-default btn-xs">Открыть ленту <span class="fa fa-external-link fa-fw"></span></a></p>
</div>

{if empty($feedlist)}
	<div class="panel-body">
		<p class="lead">В данной ленте нет записей<br />Нажмите на ссылку &quot;Добавить запись&quot;, что бы создать Вашу первую публикацию</p>
	</div>
{else}

	<table class="table table-hover hidden-xs">
		<thead>
		<tr class="active">
			<th width="55%" style="padding-left: 30px;">Заголовок</th>
			<th width="10%">Дата публикации</th>
			<th width="10%">Дата посл.изменений</th>
			<th width="25%" class="text-right">Опции</th>
		</tr>
		</thead>
		<tbody>
		{foreach from=$feedlist item=item}
			<tr>
				<td>
					{if $item['publication_status'] != "hide"}<a href="{$SCRIPT_NAME}?act=feeds&part=status_{if $item['status'] == 0}on{else}off{/if}_item&page={$feed['id']}&item={$item['id']}" class="{if $item['status'] == 0}show{else}hide{/if}-feed-element">{/if}
					<i class="fa fa-fw fa-eye{if $item['publication_status'] == "hide" || $item['status'] == 0}-slash text-muted{else} text-primary{/if}"></i>{if $item['publication_status'] != "hide"}</a>{/if}
					<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}"{if $item['publication_status'] == "hide" || $item['status'] == 0} class="text-muted"{/if}>{if $item['status'] == 0}<s>{/if}{$item['title']}{if $item['status'] == 0}</s>{/if}</a>
					<span class="pull-right small"><i class="fa fa-fw fa-eye" rel="tooltip" title="{$item['views']}" data-placement="right" data-container="body"></i></span>
					{if isset($item['tags'])}<span class="pull-right small"><i class="fa fa-fw fa-tags" rel="tooltip" title="{foreach from=$item['tags'] item=tag} #{$tag['title']} {/foreach}" data-placement="left" data-container="body"></i></span>{/if}
					{if $item['publication_status'] == "hide"}<small class="text-danger trinfo">истек период публикации</small>{/if}
				</td>
				<td class="small">c {$item['date_publications']}{if $item['date_end_publications'] != 0}<br />по {$item['date_end_publications']}{/if}</td>
				<td class="small">{$item['date_update']}</td>
				<td class="text-right">
					<div class="btn-group">
						<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><i class="fa fa-pencil-square-o fa-fw"></i>Редактировать</a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=migrate_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><i class="fa fa-random fa-fw" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Переместить публикацию"></i></a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-danger"><i class="fa fa-trash-o fa-fw"></i>Удалить</a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<ul class="list-group visible-xs">
		{foreach from=$feedlist item=item}
			<li class="list-group-item">

				<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}"{if $item['publication_status'] == "hide" || $item['status'] == 0} class="text-muted"{/if}>{if $item['status'] == 0}<s>{/if}{$item['title']}{if $item['status'] == 0}</s>{/if}</a>

				<div class="pull-right">
					<div class="btn-group">
						<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><i class="fa fa-pencil-square-o fa-fw"></i></a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=migrate_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><i class="fa fa-random fa-fw" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Переместить публикацию"></i></a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-danger"><i class="fa fa-trash-o fa-fw"></i></a>
					</div>
				</div>
			</li>
		{/foreach}
	</ul>
{/if}