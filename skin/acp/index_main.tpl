{* Разные предупреждения *}


<h3>{$part_title}</h3>

<table class="table table-hover table-condensed">
	{*<caption>Общая сводка</caption>*}
	<thead>
		<tr>
			<th>Параметр</th>
			<th>Значение</th>
		</tr>
	</thead>
	<tbody>
		{if isset($warn) && !empty($warn)}
			{foreach from=$warn item=text}
				<tr class="danger">
    				<td colspan="2" class="text-danger">{$text}</td>
				</tr>
			{/foreach}
		{/if}
		<tr>
    		<td>Ваша версия RooCMS:</td>
    		<td>{$info['roocms']}</td>
		</tr>
	</tbody>
</table>


{*
{if isset($info['last_stable'])}
	<div class="option">
		<b>Последняя версия RooCMS:</b> 	{$info['last_stable']}
	</div>
{/if}
*}