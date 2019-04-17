{* Template Feed Control *}

<div class="card-header">
	Лента "{$feed['title']}"

	<a href="/?page={$feed['alias']}" target="_blank" class="btn btn-outline-primary btn-sm float-right">Открыть ленту <span class="fa fa-external-link-alt fa-fw"></span></a>
</div>

{if empty($feedlist)}
	<div class="card-body">
		<p class="lead">В данной ленте нет записей<br />Нажмите на ссылку &quot;Добавить запись&quot;, что бы создать Вашу первую публикацию</p>
	</div>
{else}

	<table class="table table-hover d-none d-sm-table mb-0">
		<thead class="bg-light">
			<tr class="active">
				<th width="55%" class="pl-4">Заголовок</th>
				<th width="10%">Дата публикации</th>
				<th width="10%">Дата изменений</th>
				<th width="25%" class="text-right">Опции</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$feedlist item=item}
			<tr>
				<td class="align-middle">
					{if $item['publication_future']}
						<i class="fas fa-fw fa-clock text-gray"></i>
					{else}
						{if $item['publication_status'] != "hide"}<a href="{$SCRIPT_NAME}?act=feeds&part=status_{if $item['status'] == 0}on{else}off{/if}_item&page={$feed['id']}&item={$item['id']}" class="{if $item['status'] == 0}show{else}hide{/if}-feed-element">{/if}
						<i class="fas fa-fw fa-eye{if $item['publication_status'] == "hide" || $item['status'] == 0}-slash text-muted{else} text-primary{/if}"></i>{if $item['publication_status'] != "hide"}</a>{/if}
					{/if}
					<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}"{if $item['publication_status'] == "hide" || $item['status'] == 0} class="text-muted"{/if}{if $item['publication_future']} class="text-gray"{/if}>{if $item['status'] == 0}<s>{/if}{$item['title']}{if $item['status'] == 0}</s>{/if}</a>
					<span class="float-right small">
						{if $item['group_access'] != 0}<i class="fas fa-fw fa-user-secret" rel="tooltip" data-toggle="tooltip" data-placement="left" title="Есть групповые ограничения"></i>{/if}
						{if isset($item['tags'])}<i class="fas fa-fw fa-tags" rel="tooltip" title="{foreach from=$item['tags'] item=tag} #{$tag['title']} {/foreach}" data-placement="top" data-container="body"></i>{/if}
						<i class="fas fa-fw fa-eye" rel="tooltip" title="{$item['views']}" data-placement="right" data-container="body"></i>
					</span>
					{if $item['publication_status'] == "hide"}<small class="text-danger float-right">истек период публикации</small>{/if}
				</td>
				<td class="small align-middle">c {$item['date_publications']}{if $item['date_end_publications'] != 0}<br />по {$item['date_end_publications']}{/if}</td>
				<td class="small align-middle">{$item['date_update']}</td>
				<td class="text-right align-middle">
					<div class="btn-group">
						<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="btn btn-sm btn-outline-primary"><i class="far fa-edit fa-fw"></i>Редактировать</a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=migrate_item&page={$feed['id']}&item={$item['id']}" class="btn btn-sm btn-outline-primary" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Переместить публикацию"><i class="fa fa-random fa-fw"></i></a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="btn btn-sm btn-danger"><i class="far fa-trash-alt fa-fw"></i>Удалить</a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<table class="table table-hover d-block-table d-sm-none mb-0">
		<tbody>
		{foreach from=$feedlist item=item}
			<tr>
				<td class="align-middle">
					<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}"{if $item['publication_status'] == "hide" || $item['status'] == 0} class="text-muted"{/if}>{if $item['status'] == 0}<s>{/if}{$item['title']}{if $item['status'] == 0}</s>{/if}</a>
				</td>
				<td class="w-25 align-middle text-right">
					<div class="btn-group btn-group-sm">
						<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="btn btn-sm btn-outline-primary"><i class="far fa-edit fa-fw"></i></a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=migrate_item&page={$feed['id']}&item={$item['id']}" class="btn btn-sm btn-outline-primary" rel="tooltip" data-toggle="tooltip" data-placement="top" title="Переместить публикацию"><i class="fa fa-random fa-fw"></i></a>
						<a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="btn btn-sm btn-danger"><i class="far fa-trash-alt fa-fw"></i></a>
					</div>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{/if}

