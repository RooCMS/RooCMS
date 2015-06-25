{* Шаблон дизайна отображения картинок и файлов в блоках *}

{if !empty($images)}
<div class="text-center">
	{foreach from=$images item=img}
		<a href="/upload/images/{$img['resize']}" rel="img"><img src="/upload/images/{$img['thumb']}" class="img-thumbnail" style="margin: 3px 0px;"></a>
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