{* Шаблон простых страниц *}
<div id="content" class="container">
	{$content}
	{* Шаблон шаблон отображения картинок на страницах *}
	<div class="center">
		{foreach from=$images item=img}
			<a href="/upload/images/resize/{$img['filename']}" rel="img"><img src="/upload/images/thumb/{$img['filename']}" class="img-polaroid" style="margin: 3px 0px;"></a>
		{/foreach}
	</div>
</div>
