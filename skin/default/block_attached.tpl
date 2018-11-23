{* Шаблон дизайна отображения картинок и файлов в блоках *}

{if !empty($images)}
	<div class="text-center">
		{assign var=UGID value= 1|rand:399}
		{foreach from=$images item=img}
			<a href="/upload/images/{$img['resize']}" data-fancybox="gallery{$UGID}" data-caption="{$img['alt']}" title="{$img['alt']}"><img src="/upload/images/{$img['thumb']}" class="img-thumbnail" alt="{$img['alt']}" style="margin: 3px 0px;"></a>
		{/foreach}
	</div>
{/if}


{if !empty($attachfile)}
	<div class="text-left">
		<strong>Файлы:</strong>
		{foreach from=$attachfile item=file}
			<br /><a href="/upload/files/{$file['file']}" class="btn btn-xs btn-default"><i class="fa fa-fw fa-download"></i> {$file['filetitle']}</a>
		{/foreach}
	</div>
{/if}