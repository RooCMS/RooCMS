{* Шаблон для модуля: tagcloud *}

<hr />
<h4><i class="fa fa-fw fa-tags"></i> Облако тегов </h4>
<div class="row">
	<div class="col-md-8 col-md-offset-2 text-center">
		{foreach from=$tags item=tag}
			<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['ukey']}" class="btn btn-default btn-xs"><span style="font-size:{$tag['fontsize']}%;" title="{$tag['title']}"><i class="fa fa-fw fa-tag fa-va"></i>{$tag['title']}</span></a>
		{/foreach}
	</div>
</div>

