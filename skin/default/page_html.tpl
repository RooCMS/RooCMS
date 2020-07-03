{* HTML Page template *}

<div class="container mb-4 py-3">
	<div class="row">
		<div class="{if $page_alias == "index"}col-lg-9 col-md-8{else}col-12{/if}">
			{if $content != ""}
				<div class="card card-body pb-0">
					{$content}
				</div>
                        {/if}

			{* Show images and files in page *}
			{if !empty($images)}
				<div class="d-flex flex-column flex-sm-row align-content-stretch justify-content-center flex-wrap mt-1">
					{foreach from=$images item=img}
						<a href="/upload/images/{$img['resize']}" data-fancybox="gallery-{$page_alias}" data-animation-duration="300" data-caption="{$img['alt']}" title="{$img['alt']}" class="col-sm-4 col-lg-3 col-xl-2 {*flex-fill*} px-1"><img src="/upload/images/{$img['thumb']}" alt="{$img['alt']}" class="w-100 img-fluid my-1"></a>
					{/foreach}
				</div>
			{/if}

			{if !empty($attachfile)}
				<div class="d-flex flex-column flex-sm-row align-content-stretch {*justify-content-center*} flex-wrap">
					{foreach from=$attachfile item=file}
						<a href="/upload/files/{$file['file']}" class="btn btn-sm btn-outline-dark flex-fill mb-1 mx-1"><i class="fas fa-fw fa-download"></i> {$file['filetitle']}</a>
					{/foreach}
				</div>
			{/if}

			{if $page_alias == "index"}
				<h5 class="text-gray text-center my-3">Популярные новости</h5>
				{$module->load('popular_feed')}
			{/if}
		</div>
		{if $page_alias == "index"}
		<div class="col-lg-3 col-md-4">
			<div class="card">
				<div class="card-header">
					Последние новости
				</div>
			</div>
			{$module->load('last_feed')}
			<div class="card mt-3">
				<div class="card-header">
					Метки
				</div>
			</div>
			<div class="card">
				<div class="card-body">
					{$module->load('tag_cloud')}
				</div>
			</div>
		</div>
		{/if}
	</div>
</div>


