{* Информация о сервере *}

<div class="panel-heading">
	Информация о сервере
</div>

<table class="table table-hover table-condensed">
	<thead>
		<tr class="active">
			<th>Параметр</th>
			<th>Значение</th>
		</tr>
	</thead>
	<tbody>
		<tr> <td class="col-sm-4">Версия PHP:</td> <td class="col-sm-8">{$data1['php']}</td> </tr>
		<tr> <td>Версия Zend:</td> <td>{$data1['zend']}</td> </tr>
		<tr> <td>Версия MySQL:</td> <td>{$data1['mysql']}</td> </tr>
		<tr> <td>Версия RooCMS:</td> <td>{$data1['roocms']}</td> </tr>
		<tr> <td>Apache:</td> <td>{$data1['apache']}</td> </tr>
		<tr> <td>Имя сервера:</td> <td>{$data1['sn']}</td> </tr>
		<tr> <td>Адрес сервера:</td> <td>{$data1['sa']}</td> </tr>
		<tr> <td>Протокол сервера:</td> <td>{$data1['sp']}</td> </tr>
		<tr> <td>Операционная система:</td> <td>{$data1['os']}</td> </tr>
		<tr> <td>Операционная система (build):</td> <td>{$data1['uname']}</td> </tr>
		<tr> <td>Лимит памяти:</td> <td>{$data1['ml']}</td> </tr>
		<tr> <td>Максимальный размер файлов для загрузки:</td> <td>{$data1['mfs']}</td> </tr>
		<tr> <td>Максимальный размер постинга:</td> <td>{$data1['mps']}</td> </tr>
		<tr> <td>Максимально допустимое время исполнения скрипта:</td> <td>{$data1['met']} секунд</td> </tr>
		<tr> <td>Корневая директория сайта:</td> <td>{$data1['docroot']}</td> </tr>
		<tr> <td>Apache модули:</td> <td>{foreach from=$data1['apache_mods'] item=mods}<span class="badge">{$mods}</span> {/foreach}</td> </tr>
	</tbody>
</table>
<div class="panel-footer">Предопределённые переменные сервера</div>


<table class="table table-hover table-condensed">
	<thead>
		<tr class="active">
			<th>Параметр</th>
			<th>Значение</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$data2 item=svar}
			<tr>
				<td class="col-sm-4">$_SERVER['{$svar['var']}']</td>
				<td class="col-sm-8 {if $svar['value'] == "not found"}text-muted{/if}">{$svar['value']}</td>
			</tr>
		{/foreach}
	</tbody>
</table>