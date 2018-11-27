{* PHP Log Template *}

<div class="panel-heading">
	Некртические ошибки PHP
</div>
{if !empty($error)}
	<table class="table table-hover table-condensed hidden-xs">
		<thead>
			<tr class="active">
				<th width="100%">Ошибка</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$error item=e}
			<tr>
				<td>{$e}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<div class="panel-footer">
		<a href="{$SCRIPT_NAME}?act=logs&part=clear_syserrors" class="btn btn-danger"><i class="fa fa-fw fa-trash-o"></i> Очистить лог</a>
	</div>
{else}
	<div class="panel-body">
		В Логе нет записей.
	</div>
{/if}