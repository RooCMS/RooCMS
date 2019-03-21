{* PHP Log Template *}

<div class="card-header">
	Ошибки системы
</div>
{if !empty($error)}
	<table class="table table-hover mb-0">
		<thead class="bg-light">
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

	<div class="card-footer">
		<div class="row">
			<div class="col-lg-12">
				<a href="{$SCRIPT_NAME}?act=logs&part=clear_syserrors" class="btn btn-danger"><i class="far fa-fw fa-trash-alt"></i> Очистить лог</a>
			</div>
		</div>
	</div>
{else}
	<div class="card-body">
		В Логе нет записей.
	</div>
{/if}