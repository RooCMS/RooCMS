{* Action log template*}

<div class="card-header">
	Лог действий
</div>
{if !empty($datalog)}
	<table class="table table-hover d-none d-sm-table mb-0">
		<thead class="bg-light">
			<tr class="active">
				<th width="11%">Дата</th>
				<th width="9%" class="text-center">Тип события </th>
				<th width="10%" class="text-center">Пользователь</th>
				<th width="72%">Запись</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$datalog item=r}
			<tr>
				<td class="small align-middle">{$r['date_log']}</td>
				<td class="text-center align-middle"><span class="badge badge-{if $r['type_log'] == "error"}danger{elseif $r['type_log'] == "info"}info{else}light{/if}">{$r['type_log']}</span></td>
				<td class="text-center align-middle">
					{if $r['uid'] != 0}
						<a href="{$SCRIPT_NAME}?act=users&part=edit_user&uid={$r['uid']}" class="btn btn-sm btn-outline-secondary">{$r['nickname']}</a>
					{else}
						<span class="badge badge-light">Гость</span>
					{/if}
				</td>
				<td class="align-middle">
					<mark>{$r['message']}</mark>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<a href="{$SCRIPT_NAME}?act=logs&part=clear_logaction" class="btn btn-danger"><i class="far fa-fw fa-trash-alt"></i> Очистить лог</a>
			</div>
		</div>
	</div>
{else}
	<div class="card-body">
		Лог не содержит записей
	</div>
{/if}