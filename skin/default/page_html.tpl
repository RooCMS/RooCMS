{* Шаблон простых страниц *}
<div class="row">
	<div class="col-sm-12">
		{$content}
		{* Шаблон шаблон отображения картинок на страницах *}
		{if !empty($images)}
		<div class="well well-sm">
			{assign var=UGID value= 400|rand:699}
			{foreach from=$images item=img}
				<a href="/upload/images/{$img['resize']}" data-fancybox="gallery{$UGID}" data-animation-duration="300" data-caption="{$img['alt']}" title="{$img['alt']}"><img src="/upload/images/{$img['thumb']}" alt="{$img['alt']}" class="img-thumbnail" style="margin: 3px 0px;"></a>
			{/foreach}
		</div>
		{/if}

		{if !empty($attachfile)}
			<div class="well well-sm">
				<strong>Файлы:</strong>
				{foreach from=$attachfile item=file}
					<br /><a href="/upload/files/{$file['file']}" class="btn btn-sm btn-default"><i class="fa fa-fw fa-download"></i> {$file['filetitle']}</a>
				{/foreach}
			</div>
		{/if}
	</div>
</div>
