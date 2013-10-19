{* Шаблон дизайна отображения картинок в блоках *}
<div class="center">
	{foreach from=$images item=img}
		<a href="/upload/images/{$img['resize']}" rel="img"><img src="/upload/images/{$img['thumb']}" class="img-thumbnail" style="margin: 3px 0px;"></a>
	{/foreach}
</div>



