{* Файлы и форматы *}
<div class="option">
	<b>Максимальный размер файлов для загрузки:</b> {$filetypes['mfs']}
</div>
<div class="option">
	<b>Максимальный размер постинга:</b> {$filetypes['mps']}
</div>
<div class="option">
	<b>Разрешенные к загрузке форматы изображений:</b> 	
		{foreach from=$filetypes['images'] item=type}
			<img src="/img/icon/16/{$type['ico']}" border="0" width="16" height="16" alt="{$type['type']}" title="{$type['type']}" class="img"> {$type['ext']}  &nbsp;
		{/foreach}
</div>
<div class="option">
	<b>Разрешенные к загрузке форматы файлов:</b> 	
		{foreach from=$filetypes['files'] item=type}
			<img src="/img/icon/16/{$type['ico']}" border="0" width="16" height="16" alt="{$type['type']}" title="{$type['type']}" class="img"> {$type['ext']} &nbsp;
		{/foreach}
</div>