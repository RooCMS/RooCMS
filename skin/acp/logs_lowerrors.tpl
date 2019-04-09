{* PHP Log Template *}

<div class="card-header">
	Ошибки PHP
</div>
{if !empty($error)}
	<table class="table table-hover d-none d-sm-table mb-0">
		<thead class="bg-light">
			<tr class="active">
				<th width="10%">Дата</th>
				<th width="12%">Тип ошибки </th>
				<th width="3%" class="text-center">№</th>
				<th width="75%">Ошибка</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$error item=e}
			<tr>
				<td class="small align-middle">{$e[0]}</td>
				<td class="align-middle">{$e[3]}</td>
				<td class="text-center align-middle">{$e[4]}</td>
				<td class="align-middle">
					<b class="small">Файл:</b> {$e[7]} <b class="small">Строка:</b> {$e[6]}
					<br /><mark class="my-1">{$e[5]}</mark>
					<span class="small text-gray">IP: <b class="text-info">{$e[1]}</b> URI: <b>{$e[2]}</b></span>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	{foreach from=$error item=e}
		<div class="card-body border-top border-bottom d-block d-sm-none">
			<p class="card-text">
				<small class="text-muted">{$e[3]} - {$e[4]}</small>
			</p>
			<p class="card-text">
				<b class="small">Файл:</b> {$e[7]} <b class="small">Строка:</b> {$e[6]}
				<mark class="my-2">{$e[5]}</mark>
				<span class="small">IP: <b>{$e[1]}</b> URI: <b>{$e[2]}</b></span>
			</p>
		</div>
	{/foreach}

	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<a href="{$SCRIPT_NAME}?act=logs&part=clear_lowerrors" class="btn btn-danger"><i class="far fa-fw fa-trash-alt"></i> Очистить лог</a>
			</div>
		</div>
	</div>
{else}
	<div class="card-body">
		В Логе нет записей.
	</div>
{/if}