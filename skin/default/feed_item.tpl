{* Feed item *}
{if isset($images[0]['resize'])}
<style>
	.feed-item-header::after {
		background: transparent url('/upload/images/{$images[0]['resize']}') no-repeat center 50%;
		background-size: cover;
	}
</style>
{/if}

<div class="container">
	<div class="row" id="item_{$item['id']}">
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-header feed-item-header">
					<h1 class="card-title mb-0">{$item['title']}</h1>
					<span class="small">
						<i class="fas fa-fw fa-calendar" title="Дата публикации"></i> {$item['datepub']}
						{if $item['views'] != 0} <i class="fas fa-fw fa-eye" title="Просмотрено раз"></i> {$item['views']}{/if}
					</span>

					{if !empty($item['tags'])}
					<div class="float-right small mt-n2">
						{foreach from=$item['tags'] item=tag}
							<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['title']}" class="btn btn-outline-gray btn-sm">#{$tag['title']}</a>
						{/foreach}
					</div>
					{/if}
				</div>
				<div class="card-body">
					{if isset($smarty.get.search)}
						{$item['full_item']|highlight:$smarty.get.search}
					{else}
						{$item['full_item']}
					{/if}

					<div class="card-text small text-right">
						{if $item['author_id'] != 0}
							{if file_exists("upload/images/{$item['author']['avatar']}")}
								<img src="/upload/images/{$item['author']['avatar']}" class="rounded-circle border float-right ml-2" height="45" alt="{$item['author']['nickname']}">
							{else}
								<i class="far fa-fw fa-user-circle fa-4x pull-left" title="Автор"></i>
							{/if}
							<div class="pt-1">Автор: <b class="ubuntu">{$item['author']['nickname']}</b></div>
							<div class="ubuntu font-italic mt-1">{$item['author']['slogan']}</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	{if !empty($images) || !empty($attachfile)}
	<div class="col-sm-3">
		{* Attached Images *}
		{if !empty($images)}
			<div class="text-center">
				{assign var=UGID value= 700|rand:999}
				{foreach from=$images item=img}
					<a href="/upload/images/{$img['resize']}" data-fancybox="gallery{$UGID}" data-animation-duration="300" data-caption="{$img['alt']}" title="{if $img['alt'] != ""}{$img['alt']}{else}{$item['title']}{/if}"><img src="/upload/images/{$img['thumb']}" class="img-thumbnail" style="margin: 3px 0;" alt="{if $img['alt'] != ""}{$img['alt']}{else}{$item['title']}{/if}"></a>
				{/foreach}
			</div>
		{/if}
		{* Attached Files *}
		{if !empty($attachfile)}
			<div class="text-left">
				<strong>Файлы:</strong>
				{foreach from=$attachfile item=file}
					<br /><a href="/upload/files/{$file['file']}" class="btn btn-sm btn-default"><i class="fa fa-fw fa-download"></i> {$file['filetitle']}</a>
				{/foreach}
			</div>
		{/if}
	</div>
	{/if}
	<div class="col-sm-{if !empty($images) || !empty($attachfile)}9{else}12{/if} feed-item-content">


	</div>
</div>

<hr />
<div class="row">
	<div class="col-xs-6 text-left text-truncate">
		{if isset($item['prev'])}
			<small>Ранее {$item['prev']['datepub']}</small>
			<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['prev']['id']}" class="text-uppercase" title="{$item['prev']['title']}"><i class="fa fa-angle-left fa-3x pull-left"></i>
				{if isset($item['prev']['image'][0])}<img src="/upload/images/{$item['prev']['image'][0]['thumb']}" class="img-rounded pull-left feed-image-pn" alt="{$item['prev']['title']}">{/if}
				<br /><span class="hidden-xs">{$item['prev']['title']}</span></a>
		{/if}
	</div>

	<div class="col-xs-6 text-right text-truncate">
		{if isset($item['next'])}
			<small>Далее {$item['next']['datepub']}</small>
			<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['next']['id']}" class="text-uppercase" title="{$item['next']['title']}"><i class="fa fa-angle-right fa-3x pull-right"></i>
				{if isset($item['next']['image'][0])}<img src="/upload/images/{$item['next']['image'][0]['thumb']}" class="img-rounded pull-right feed-image-pn" alt="{$item['next']['title']}">{/if}
				<br /><span class="hidden-xs">{$item['next']['title']}</span></a>
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
				<a href="{$SCRIPT_NAME}?page={$feed['alias']}&id={$an['id']}">
					{if isset($an['image'][0])}<img src="/upload/images/{$an['image'][0]['thumb']}" class="img-rounded feed-more-preview">{/if}<br />{$an['title']}</a>
				<br /><small>{$an['datepub']}</small>
			</div>
		{/foreach}
	</div>
{/if}
