{* Шаблон отображения элемента ленты *}
<div id="item_{$item['id']}">
	{if isset($images[0]['resize'])}
	<style>
		.feed-item-head::after {
			background: transparent url('/upload/images/{$images[0]['resize']}') no-repeat center 50%;
			background-size: cover;
		}
	</style>

	<div class="feed-item-head">
	{/if}
		<h1>{$item['title']}</h1>
		<div class="pull-right">
			{if !empty($item['tags'])}
				<span class="small">
					{foreach from=$item['tags'] item=tag}
						<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['title']}" class="btn btn-default btn-xs"><i class="fa fa-fw fa-tag fa-va"></i>{$tag['title']}</a>
					{/foreach}
				</span>
			{/if}
		</div>
		<small>
			<i class="fa fa-fw fa-calendar" title="Дата публикации"></i> {$item['datepub']}
			{if $item['views'] != 0} <i class="fa fa-fw fa-eye" title="Просмотрено раз"></i> {$item['views']}{/if}
			{*{if $item['author_id'] != 0}<br /> <i class="fa fa-fw fa-user-circle-o" title="Автор"></i> {$item['author']['nickname']}{/if}*}
		</small>

	{if isset($images[0]['resize'])}</div>{else}<hr />{/if}


	{if isset($smarty.get.search)}
		{$item['full_item']|highlight:$smarty.get.search}
	{else}
		{$item['full_item']}
	{/if}


	{* Шаблон отображения картинок в элементах ленты *}
	{if !empty($images)}
	<div class="text-center">
		{foreach from=$images item=img}
			<a href="/upload/images/{$img['resize']}" rel="img" title="{$item['title']}"><img src="/upload/images/{$img['thumb']}" class="img-thumbnail" style="margin: 3px 0;" alt="{$item['title']}"></a>
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

	{if $item['author_id'] != 0}
		<h5>Автор:</h5>
		{if file_exists("upload/images/{$item['author']['avatar']}")}
			<img src="/upload/images/{$item['author']['avatar']}" class="img-circle pull-left mauth-avatar" height="55">
		{else}
			<i class="fa fa-fw fa-user-circle-o fa-4x pull-left" title="Автор"></i>
		{/if}
		<b class="ubuntu">{$item['author']['nickname']}</b>
		<p style="min-height: 30px;"><i class="ubuntu">{$item['author']['slogan']}</i></p>
	{/if}

	<hr />
	<div class="row">
		<div class="col-xs-5 text-left">
			{if isset($item['prev'])}
				<small>Ранее {$item['prev']['datepub']}</small>
				<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['prev']['id']}"><i class="fa fa-angle-left fa-3x pull-left"></i>
					{if isset($item['prev']['image'][0])}<img src="/upload/images/{$item['prev']['image'][0]['thumb']}" class="img-rounded pull-left feed-image-pn">{/if}
				<br />{$item['prev']['title']}</a>
			{/if}
		</div>
		<div class="col-xs-2 text-center">
			<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}" class="btn btn-xs btn-default"><span class="fa fa-sort-asc fa-fw"></span><br />Вернуться</a>
		</div>
		<div class="col-xs-5 text-right">
			{if isset($item['next'])}
				<small>Далее {$item['next']['datepub']}</small>
				<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['next']['id']}"><i class="fa fa-angle-right fa-3x pull-right"></i>
					{if isset($item['next']['image'][0])}<img src="/upload/images/{$item['next']['image'][0]['thumb']}" class="img-rounded pull-right feed-image-pn">{/if}
				<br />{$item['next']['title']}</a>
			{/if}
		</div>
	</div>

	{if !empty($more)}
		<hr />
		<div class="row">
			<div class="col-sm-12">
				<h4>Вам понравится:</h4>
			</div>
		</div>
		<div class="row">
			{foreach from=$more item=an key=i}
				<div class="col-sm-4 text-center">
					{*<style>
						{if isset($an['image'][0])}
							{literal}
							.feed-more-preview-{/literal}{$i}{literal} {
								width: 90%;
								height: 120px;
								background: transparent url('/upload/images/{/literal}{$an['image'][0]['thumb']}{literal}') no-repeat local center 50%;
								background-size: cover;
							}
							{/literal}
						{/if}
					</style>
					<div class="feed-more-preview-{$i}"></div>*}
					<a href="{$SCRIPT_NAME}?page={$feed['alias']}&id={$an['id']}">
						{if isset($an['image'][0])}<img src="/upload/images/{$an['image'][0]['thumb']}" class="img-rounded feed-more-preview">{/if}<br />{$an['title']}</a>
					<br /><small>{$an['datepub']}</small>
				</div>
			{/foreach}
		</div>
	{/if}

</div>