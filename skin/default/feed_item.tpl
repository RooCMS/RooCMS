{* Feed item *}

<div class="container">
	<div class="row" id="item_{$item['id']}">
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-header d-flex flex-column flex-md-row flex-wrap">
					<h1 class="card-title w-100 mb-0">{$item['title']}</h1>
					<span class="small mt-2 mb-3 mb-md-0">
						<i class="fas fa-fw fa-calendar" title="Дата публикации"></i> {$item['datepub']}
						{if $item['views'] != 0} <i class="fas fa-fw fa-eye" title="Просмотрено раз"></i> {$item['views']}{/if}
					</span>

					{* Tags *}
					{if !empty($item['tags'])}
					<div class="ml-md-auto small text-center">
						{foreach from=$item['tags'] item=tag}
							<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['title']}" class="btn btn-outline-gray btn-sm">#{$tag['title']}</a>
						{/foreach}
					</div>
					{/if}
				</div>
				<div class="card-body">
					<div class="row">
						<div class="{if (!empty($images) && $images|@count > 2) || !empty($attachfile)}col-md-8{else}col-12{/if}">
							{if (!empty($images) && $images|@count <= 2)}
								<a href="/upload/images/{$images[0]['resize']}" data-fancybox="gallery-{$page_alias}{$item['id']}" data-animation-duration="300" data-caption="{$images[0]['alt']}" title="{if $images[0]['alt'] != ""}{$images[0]['alt']}{else}{$item['title']|escape}{/if}" class="flex-fill mb-3 roocms-feeditem-images"><img src="/upload/images/{$images[0]['resize']}" alt="{if $images[0]['alt'] != ""}{$images[0]['alt']}{else}{$item['title']|escape}{/if}" class="w-100 img-fluid mb-3"></a>
							{/if}
							{if isset($smarty.get.search)}
								{$item['full_item']|highlight:$smarty.get.search}
							{else}
								{$item['full_item']}
							{/if}
							{if (!empty($images) && $images|@count == 2)}
								<a href="/upload/images/{$images[1]['resize']}" data-fancybox="gallery-{$page_alias}{$item['id']}" data-animation-duration="300" data-caption="{$images[1]['alt']}" title="{if $images[1]['alt'] != ""}{$images[1]['alt']}{else}{$item['title']|escape}{/if}" class="flex-fill mb-3 roocms-feeditem-images"><img src="/upload/images/{$images[1]['resize']}" alt="{if $images[1]['alt'] != ""}{$images[1]['alt']}{else}{$item['title']|escape}{/if}" class="w-100 img-fluid mb-3"></a>
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
						{if (!empty($images) && $images|@count > 2) || !empty($attachfile)}
						<div class="col-md-4">
							{* Attached Images *}
							{if !empty($images)}
								<div class="d-flex flex-column flex-sm-row align-content-stretch justify-content-center flex-wrap mt-1 roocms-feedimg">
									{foreach from=$images item=img name=aimgs}
										{*|| $smarty.foreach.aimgs.iteration is div by 3*}
										<a href="/upload/images/{$img['resize']}" data-fancybox="gallery-{$page_alias}{$item['id']}" data-animation-duration="300" data-caption="{$img['alt']}" title="{if $img['alt'] != ""}{$img['alt']}{else}{$item['title']|escape}{/if}" class="flex-fill px-1 mb-1 {if $smarty.foreach.aimgs.total >= 4 && (!$smarty.foreach.aimgs.first && !$smarty.foreach.aimgs.last)}roocms-feeditem-images{/if}"><img src="/upload/images/{$img['thumb']}" alt="{if $img['alt'] != ""}{$img['alt']}{else}{$item['title']|escape}{/if}" class="w-100 img-fluid mb-1"></a>
									{/foreach}
								</div>
							{/if}

							{* Attached Files *}
							{if !empty($attachfile)}
								<strong class="small">Файлы:</strong>
								<div class="d-flex flex-column flex-sm-row align-content-stretch {*justify-content-center*} flex-wrap mb-3">
									{foreach from=$attachfile item=file}
										<br /><a href="/upload/files/{$file['file']}" class="btn btn-sm btn-outline-gray flex-fill"><i class="fas fa-fw fa-download"></i> {$file['filetitle']}</a>
									{/foreach}
								</div>
							{/if}
						</div>
						{/if}
					</div>
				</div>
			</div>

			{* Prev / Next item *}
			{if isset($item['prev']) || isset($item['next'])}
				<div class="row mb-3">
					<div class="col-6 text-left text-truncate">
						{if isset($item['prev'])}
							<small>Ранее {$item['prev']['datepub']}</small>
							<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['prev']['id']}" class="text-uppercase" id="PrevFeedItem" title="{$item['prev']['title']}">
								<i class="fas fa-angle-left fa-3x float-left mr-1"></i>
								{if isset($item['prev']['image'][0])}<img src="/upload/images/{$item['prev']['image'][0]['thumb']}" class="rounded float-left mr-2 roocms-feeditem-pn d-none d-md-block" alt="{$item['prev']['title']}">{/if}
								<br />{$item['prev']['title']}</a>
						{/if}
					</div>
					<div class="col-6 text-right text-truncate">
						{if isset($item['next'])}
							<small>Далее {$item['next']['datepub']}</small>
							<a href="{$SCRIPT_NAME}?page={$feed['alias']}{if isset($smarty.get.pg)}&pg={$smarty.get.pg}{/if}&id={$item['next']['id']}" class="text-uppercase" id="NextFeedItem" title="{$item['next']['title']}">
								<i class="fas fa-angle-right fa-3x float-right ml-1"></i>
								{if isset($item['next']['image'][0])}<img src="/upload/images/{$item['next']['image'][0]['thumb']}" class="rounded float-right ml-2 roocms-feeditem-pn d-none d-md-block" alt="{$item['next']['title']}">{/if}
								<br />{$item['next']['title']}</a>
						{/if}
					</div>
				</div>
			{/if}

			{* More *}
			{if !empty($more)}
				<div class="row mt-5">
					<div class="col-12 text-center">
						<h5>Интересное</h5>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-12">
						<div class="card-group">
						{foreach from=$more item=an key=i}
							<div class="card">
								{if isset($an['image'][0])}
									<a href="{$SCRIPT_NAME}?page={$feed['alias']}&id={$an['id']}"><img src="/upload/images/{$an['image'][0]['thumb']}" class="card-img-top roocms-grayscale" alt="{$an['image'][0]['alt']}"></a>
								{/if}
								<div class="card-body d-flex flex-column">
									<h5 class="card-title text-center"><a href="{$SCRIPT_NAME}?page={$feed['alias']}&id={$an['id']}">{$an['title']}</a></h5>
									<div class="card-text text-center small text-gray mt-auto">{$an['datepub']}</div>
								</div>
							</div>
						{/foreach}
						</div>
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>


