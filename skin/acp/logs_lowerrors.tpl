{* PHP Log Template *}

<div class="panel-heading">
	Ошибки PHP
</div>
{if !empty($error)}
	<table class="table table-hover table-condensed hidden-xs">
		<thead>
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
				<td class="small">{$e[0]}</td>
				<td>{$e[1]}</td>
				<td class="text-center">{$e[2]}</td>
				<td>
					<b class="small">Файл:</b> {$e[5]} <b class="small">Строка:</b> {$e[4]}
					<br /><mark>{$e[3]}</mark>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<div class="panel-footer">
		<a href="{$SCRIPT_NAME}?act=logs&part=clear_lowerrors" class="btn btn-danger"><i class="fa fa-fw fa-trash-o"></i> Очистить лог</a>
	</div>
{else}
	<div class="panel-body">
		В Логе нет записей.
	</div>
{/if}