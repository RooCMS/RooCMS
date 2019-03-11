{* Файлы и форматы *}
<div class="card">
	<div class="card-header">
		Информация о файлах
	</div>

	<table class="table table-hover table-condensed mb-0">
		<thead class="bg-light">
		<tr class="active">
			<th width="40%">Параметр</th>
			<th width="60%">Значение</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>Максимально допустимый размер файлов для загрузки:</td>
			<td>{$filetypes['mfs']}</td>
		</tr>
		<tr>
			<td>Максимально допустимый размер постинга:</td>
			<td>{$filetypes['mps']}</td>
		</tr>
		<tr>
			<td>Разрешенные к загрузке форматы изображений:</td>
			<td>
				{foreach from=$filetypes['images'] item=type}
					<span class="badge badge-primary">{if file_exists("skin/acp/img/icon/16/{$type['ico']}")}<img src="skin/acp/img/icon/16/{$type['ico']}" border="0" width="16" height="16" alt="{$type['mime_type']}" title="{$type['mime_type']}" rel="tooltip" data-placement="top">{else}<span class="fas fa-file-image fa-fw fa-lg" rel="tooltip" title="{$type['mime_type']}" data-placement="top"></span>{/if} {$type['ext']}</span>
				{/foreach}
			</td>
		</tr>
		<tr>
			<td>Разрешенные к загрузке форматы файлов:</td>
			<td>
				{foreach from=$filetypes['files'] item=type}
					<span class="badge badge-primary">{if file_exists("skin/acp/img/icon/16/{$type['ico']}")}<img src="skin/acp/img/icon/16/{$type['ico']}" border="0" width="16" height="16" alt="{$type['mime_type']}" title="{$type['mime_type']}" rel="tooltip" data-placement="top">{else}<span class="fas fa-file-code fa-fw fa-lg" rel="tooltip" title="{$type['mime_type']}" data-placement="top"></span>{/if} {$type['ext']}</span>
				{/foreach}
			</td>
		</tr>
		</tbody>
	</table>
</div>