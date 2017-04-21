{* Шаблон отображения элемента ленты *}
<div id="item_{$item['id']}">
	<h1>{$item['title']}</h1>
	<small><i class="fa fa-fw fa-calendar"></i> {$item['datepub']}</small>
	<div class="pull-right">
		{if !empty($item['tags'])}
			<span class="small">
				{foreach from=$item['tags'] item=tag}
					<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['title']}" class="btn btn-default btn-xs"><i class="fa fa-fw fa-tag fa-va"></i>{$tag['title']}</a>
				{/foreach}
			</span>
		{/if}
	</div>
	<hr>
	{$item['full_item']}

	{* Шаблон отображения картинок в элементах ленты *}
	{if !empty($images)}
	<div class="text-center">
		{foreach from=$images item=img}
			<a href="/upload/images/{$img['resize']}" rel="img" title="{$item['title']}"><img src="/upload/images/{$img['thumb']}" class="img-thumbnail" style="margin: 3px 0px;" alt="{$item['title']}"></a>
		{/foreach}
	</div>
	{/if}
	{if !empty($attachfile)}
		<div class="text-left">
			<strong>Файлы:</strong>
			{foreach from=$attachfile item=file}
				<br /><a href="/upload/files/{$file['file']}" class="btn btn-xs btn-default"><i class="fa fa-fw fa-download"></i> {$file['file']}</a>
			{/foreach}
		</div>
	{/if}

	<div class="row">
		<div class="col-sm-6 col-sm-offset-6 text-right">
			<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}" class="btn btn-xs btn-primary"><span class="fa fa-chevron-circle-left fa-fw"></span> Вернуться</a>
		</div>
	</div>

	{if isset($item['prev']) || isset($item['next'])}
		<hr />
		<div class="row">
			<div class="col-xs-6 text-left">
				{if isset($item['prev'])}

					<small>Ранее {$item['prev']['datepub']}</small>
					<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['prev']['id']}"><i class="fa fa-angle-left fa-3x pull-left"></i>
					<br />{$item['prev']['title']}</a>

				{/if}
			</div>
			<div class="col-xs-6 text-right">
				{if isset($item['next'])}
					<small>Далее {$item['next']['datepub']}</small>
					<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['next']['id']}"><i class="fa fa-angle-right fa-3x pull-right"></i>
					<br />{$item['next']['title']}</a>
				{/if}
			</div>
		</div>
	{/if}

</div>