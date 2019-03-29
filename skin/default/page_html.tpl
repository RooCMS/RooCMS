{* HTML Page template *}

<div class="container mb-4 py-3 bg-white border">
	<div class="row">
		<div class="col-12">

			{$content}

			{* Шаблон шаблон отображения картинок на страницах *}
			{if !empty($images)}
				{assign var=UGID value= 400|rand:699}
				{foreach from=$images item=img}
					<a href="/upload/images/{$img['resize']}" data-fancybox="gallery{$UGID}" data-animation-duration="300" data-caption="{$img['alt']}" title="{$img['alt']}"><img src="/upload/images/{$img['thumb']}" alt="{$img['alt']}" class="img-fluid my-1"></a>
				{/foreach}
			{/if}

			{if !empty($attachfile)}
				<p>
					<strong>Файлы:</strong>
					{foreach from=$attachfile item=file}
						<br /><a href="/upload/files/{$file['file']}" class="btn btn-sm btn-default"><i class="fa fa-fw fa-download"></i> {$file['filetitle']}</a>
					{/foreach}
				</p>
			{/if}
		</div>
	</div>
</div>


