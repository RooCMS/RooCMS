{* Шаблон управления лентой *}

<h3>Лента "{$feed['title']}"</h3>

{if empty($feedlist)}
<p class="lead">В данной ленте пока что нет элементов
<br />Нажмите на ссылку "Добавить элемент", что бы внести в ленту первый элемент</p>
{else}
<div class="row hidden-xs">
	<div class="col-md-12">
		<table class="table table-hover table-condensed">
			<thead>
				<tr>
					<th width="50%">Заголовок</th>
					<th width="10%">Дата публикации</th>
					<th width="10%">Дата посл.изменений</th>
					<th width="30%">Опции</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$feedlist item=item}
        			<tr>
            			<td><a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}">{$item['title']}</a></td>
            			<td class="small">{$item['date_publications']}</td>
            			<td class="small">{$item['date_update']}</td>
            			<td>
							<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><span class="icon-edit icon-fixed-width"></span>Редактировать</a></nobr>
							<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-danger"><span class="icon-trash icon-fixed-width"></span>Удалить</a></nobr>
            			</td>
        			</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>

{foreach from=$feedlist item=item}
<div class="panel panel-default visible-xs">
    <div class="panel-heading">
		<a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}" class="panel-title">{$item['title']}</a>
    </div>
	<div class="panel-body">
		<small class="pull-right">Пуб: {$item['date_publications']}
		<br />Ред: {$item['date_update']}</small>
	</div>
	<div class="panel-footer text-right">
		<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><span class="icon-edit icon-fixed-width"></span>Редактировать</a></nobr>
		<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-danger"><span class="icon-trash icon-fixed-width"></span>Удалить</a></nobr>
	</div>
</div>
{/foreach}

{/if}