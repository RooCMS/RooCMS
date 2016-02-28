{* Значение PHP переменных *}

<div class="panel-heading">
	Значение PHP переменных
</div>

<table class="table table-hover table-condensed">
	{*<caption>Общая сводка</caption>*}
	<thead>
		<tr class="active">
			<th>Параметр</th>
			<th>Локальное значение</th>
			<th class="visible-lg">Значение на сервере</th>
			<th class="hidden-xs">Разрешения</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$inivars item=inival key=ininame}
		<tr{if $inival['local_value'] != $inival['global_value']} class="success"{/if}>
    		<td class="col-xs-2">{$ininame}</td>
    		<td class="col-xs-4{if $inival['local_value'] != $inival['global_value']} text-success bold{/if}">{$inival['local_value']|htmlspecialchars}{if $inival['local_value'] != $inival['global_value']}<small><br />{if trim($inival['global_value']) != ""}{$inival['global_value']}{else}пустое значение{/if}</small>{/if}</td>
    		<td class="col-xs-4 visible-lg">{$inival['global_value']|htmlspecialchars}</td>
    		<td class="col-xs-2 hidden-xs">
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