{* HTML Page template *}

<div class="container mb-4 py-3">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body pb-0">
					{$content}
				</div>
			</div>


			{* Шаблон шаблон отображения картинок на страницах *}
			{if !empty($images)}
				<div class="d-flex flex-column flex-sm-row align-content-stretch justify-content-center flex-wrap">
					{assign var=UGID value= 400|rand:699}
					{foreach from=$images item=img}
						<a href="/upload/images/{$img['resize']}" data-fancybox="gallery{$UGID}" data-animation-duration="300" data-caption="{$img['alt']}" title="{$img['alt']}" class="col-sm-4 col-lg-3 col-xl-2 {*flex-fill*} px-1"><img src="/upload/images/{$img['thumb']}" alt="{$img['alt']}" class="w-100 img-fluid my-1"></a>
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
		</div>
	</div>
</div>


