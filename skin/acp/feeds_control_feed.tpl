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
					<th width="55%">Заголовок</th>
					<th width="10%">Дата публикации</th>
					<th width="10%">Дата посл.изменений</th>
					<th width="25%">Опции</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$feedlist item=item}
        			<tr>
            			<td><span class="icon-fixed-width icon-eye-{if $item['status'] == "hide"}close text-muted{else}open text-primary{/if}"></span> <a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}"{if $item['status'] == "hide"} class="text-muted"{/if}>{$item['title']}</a></td>
            			<td class="small">c {$item['date_publications']}{if $item['date_end_publications'] != 0}<br />по {$item['date_end_publications']}{/if}</td>
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
		<span class="icon-fixed-width icon-eye-{if $item['status'] == "hide"}close text-muted{else}open text-primary{/if}"></span> <a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" title="{$item['title']}" class="panel-title">{$item['title']}</a>
	</div>
	<div class="panel-body">
		<small class="pull-right">Пуб: c {$item['date_publications']}{if $item['date_end_publications'] != 0} по {$item['date_end_publications']}{/if}
		<br />Ред: {$item['date_update']}</small>
	</div>
	<div class="panel-footer text-right">
		<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=edit_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-default"><span class="icon-edit icon-fixed-width"></span>Редактировать</a></nobr>
		<nobr><a href="{$SCRIPT_NAME}?act=feeds&part=delete_item&page={$feed['id']}&item={$item['id']}" class="btn btn-xs btn-danger"><span class="icon-trash icon-fixed-width"></span>Удалить</a></nobr>
	</div>
</div>
{/foreach}

{/if}