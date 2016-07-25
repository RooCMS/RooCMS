{* Разные предупреждения *}
<div class="row">
	<div class="col-sm-12">
		{if isset($warning_subj) && !empty($warning_subj)}
			{foreach from=$warning_subj item=text}
				<div class="alert alert-danger">
					<i class="fa fa-fw fa-exclamation-circle"></i> {$text}
				</div>
			{/foreach}
		{/if}
	</div>
</div>


<div class="panel panel-default">

	<div class="panel-heading">
		Сводка по сайту
	</div>

	<table class="table table-hover table-condensed">
		{*<caption>Общая сводка</caption>*}
		<thead>
		<tr class="active">
			<th>Параметр</th>
			<th>Значение</th>
		</tr>
		</thead>
		<tbody>
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