{* Разные предупреждения *}
<div class="card">

	<div class="card-header">
		Сводка по сайту
	</div>

	<table class="table table-hover table-condensed mb-0">
		{*<caption>Общая сводка</caption>*}
		<thead class="bg-light">
		<tr class="active">
			<th>Параметр</th>
			<th>Значение</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>Название сайта:</td>
			<td>{$info['sitetitle']}</td>
		</tr>
		<tr>
			<td>Адрес сайта:</td>
			<td>{$info['sitedomain']}</td>
		</tr>
		<tr>
			<td>E-mail администратора:</td>
			<td>{$info['email']}</td>
		</tr>
		<tr>
			<td>Резервный E-mail:</td>
			<td>{$info['sysemail']}</td>
		</tr>
		<tr>
			<td>Ваша версия RooCMS:</td>
			<td>{$info['roocms']}</td>
		</tr>
		</tbody>
	</table>
</div>

{*
{if isset($info['last_stable'])}
	<div class="option">
		<b>Последняя версия RooCMS:</b> 	{$info['last_stable']}
	</div>
{/if}
*}