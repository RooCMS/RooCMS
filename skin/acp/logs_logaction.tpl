{* Action log template*}

<div class="panel-heading">
	Лог действий
</div>
{if !empty($datalog)}
	<table class="table table-hover table-condensed {*table-striped*} hidden-xs">
		<thead>
			<tr class="active">
				<th width="11%">Дата</th>
				<th width="9%" class="text-center">Тип события </th>
				<th width="10%">Пользователь</th>
				<th width="72%">Запись</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$datalog item=r}
			<tr>
				<td class="small">{$r['date_log']}</td>
				<td class="text-center"><span class="label label-{if $r['type_log'] == "error"}danger{elseif $r['type_log'] == "info"}info{else}default{/if}">{$r['type_log']}</span></td>
				<td><a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$r['uid']}" class="btn btn-sm btn-link">{$r['nickname']}</a></td>
				<td>
					<mark>{$r['message']}</mark>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<div class="panel-footer">
		<a href="{$SCRIPT_NAME}?act=logs&part=clear_logaction" class="btn btn-danger"><i class="fa fa-fw fa-trash-o"></i> Очистить весь лог</a>
	</div>
{else}
	<div class="panel-body">
		Лог не содержит записей
	</div>
{/if}