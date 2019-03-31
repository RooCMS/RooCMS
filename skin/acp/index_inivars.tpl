{* Значение PHP переменных *}
<div class="card">
	<div class="card-header">
		Значение PHP переменных
	</div>

	<table class="table table-hover table-condensed mb-0">
		{*<caption>Общая сводка</caption>*}
		<thead class="bg-light">
		<tr class="active">
			<th>Параметр</th>
			<th>Локальное значение</th>
			<th class="d-none d-lg-table-cell">Значение на сервере</th>
			<th class="d-none d-sm-table-cell">Разрешения</th>
		</tr>
		</thead>
		<tbody>
		{foreach from=$inivars item=inival key=ininame}
			<tr{if $inival['local_value'] != $inival['global_value']} class="success"{/if}>
				<td class="w-25">{$ininame}</td>
				<td class="w-25{if $inival['local_value'] != $inival['global_value']} text-success font-weight-bold{/if} text-break">
					{$inival['local_value']|htmlspecialchars}{if $inival['local_value'] != $inival['global_value']}
						<small><br />{if trim($inival['global_value']) != ""}{$inival['global_value']}{else}пустое значение{/if}</small>{/if}
				</td>
				<td class="w-25 d-none d-lg-table-cell text-break">{$inival['global_value']|htmlspecialchars}</td>
				<td class="d-none d-sm-table-cell">
					{if $inival['access'] == 1}
						Через пользовательские скрипты
					{elseif $inival['access'] == 2 || $inival['access'] == 6}
						<code>.htaccess</code>, <code>php.ini</code> или <code>httpd.conf</code>
					{elseif $inival['access'] == 4}
						<code>php.ini</code> или <code>httpd.conf</code>
					{elseif $inival['access'] == 7}
						<span class="text-primary">Полный доступ</span>
					{else}
						{$inival['access']}
					{/if}
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>