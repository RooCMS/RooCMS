{* Информация о сервере *}

<h3>Информация о сервере</h3>
<table class="table table-hover table-condensed">
	<thead>
		<tr>
			<th>Параметр</th>
			<th>Значение</th>
		</tr>
	</thead>
	<tbody>
		<tr> <td>Версия PHP:</td> <td>{$version['php']}</td> </tr>
		<tr> <td>Версия Zend:</td> <td>{$version['zend']}</td> </tr>
		<tr> <td>Версия MySQL:</td> <td>{$version['mysql']}</td> </tr>
		<tr> <td>Версия RooCMS:</td> <td>{$version['roocms']}</td> </tr>
		<tr> <td>Apache:</td> <td>{$version['apache']}</td> </tr>
		<tr> <td>Имя сервера:</td> <td>{$version['sn']}</td> </tr>
		<tr> <td>Адрес сервера:</td> <td>{$version['sa']}</td> </tr>
		<tr> <td>Протокол сервера:</td> <td>{$version['sp']}</td> </tr>
		<tr> <td>Операционная система:</td> <td>{$version['os']}</td> </tr>
		<tr> <td>Операционная система (build):</td> <td>{$version['uname']}</td> </tr>
		<tr> <td>Лимит памяти:</td> <td>{$version['ml']}</td> </tr>
		<tr> <td>Максимальный размер файлов для загрузки:</td> <td>{$version['mfs']}</td> </tr>
		<tr> <td>Максимальный размер постинга:</td> <td>{$version['mps']}</td> </tr>
		<tr> <td>Максимально допустимое время исполнения скрипта:</td> <td>{$version['met']} секунд</td> </tr>
		<tr> <td>Корневая директория сайта:</td> <td>{$version['docroot']}</td> </tr>
	</tbody>
</table>