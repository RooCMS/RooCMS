{* Файлы и форматы *}

<div class="panel-heading">
	{$part_title}
</div>

<table class="table table-hover table-condensed">
	<thead>
		<tr class="active">
			<th>Параметр</th>
			<th>Значение</th>
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
				<ul class="list-unstyled">
					{foreach from=$filetypes['images'] item=type}
						<li>{if file_exists("img/icon/16/{$type['ico']}")}<img src="img/icon/16/{$type['ico']}" border="0" width="16" height="16" alt="{$type['mime_type']}" title="{$type['mime_type']}" rel="tooltip" title="{$type['mime_type']}" data-placement="top">{else}<span class="fa fa-file-o fa-fw" style="font-size: 16px;" rel="tooltip" title="{$type['mime_type']}" data-placement="top"></span>{/if} {$type['ext']}</li>
					{/foreach}
					</ul>
			</td>
		</tr>
		<tr>
			<td>Разрешенные к загрузке форматы файлов:</td>
			<td>
				<ul class="list-unstyled">
					{foreach from=$filetypes['files'] item=type}
						<li>{if file_exists("img/icon/16/{$type['ico']}")}<img src="img/icon/16/{$type['ico']}" border="0" width="16" height="16" alt="{$type['mime_type']}" title="{$type['mime_type']}" rel="tooltip" title="{$type['mime_type']}" data-placement="top">{else}<span class="fa fa-file-o fa-fw" style="font-size: 16px;" rel="tooltip" title="{$type['mime_type']}" data-placement="top"></span>{/if} {$type['ext']}</li>
					{/foreach}
					</ul>
			</td>
		</tr>
	</tbody>
</table>