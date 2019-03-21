{* PHP Log Template *}

<div class="card-header">
	Ошибки PHP
</div>
{if !empty($error)}
	<table class="table table-hover d-none d-sm-table mb-0">
		<thead class="bg-light">
			<tr class="active">
				<th width="10%">Дата</th>
				<th width="15%">Тип ошибки </th>
				<th width="3%" class="text-center">№</th>
				<th width="72%">Ошибка</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$error item=e}
			<tr>
				<td class="small align-middle">{$e[0]}</td>
				<td class="align-middle">{$e[1]}</td>
				<td class="text-center align-middle">{$e[2]}</td>
				<td class="align-middle">
					<b class="small">Файл:</b> {$e[5]} <b class="small">Строка:</b> {$e[4]}
					<br /><mark>{$e[3]}</mark>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	{foreach from=$error item=e}
		<div class="card-body border-top border-bottom d-block d-sm-none">
			<p class="card-text">
				<small class="text-muted">{$e[1]} - {$e[2]}</small>
			</p>
			<p class="card-text">
				<b class="small">Файл:</b> {$e[5]} <b class="small">Строка:</b> {$e[4]}
				<mark>{$e[3]}</mark>
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