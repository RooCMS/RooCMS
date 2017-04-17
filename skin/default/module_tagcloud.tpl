{* Шаблон для модуля: tagcloud *}

<hr />
<div class="row">
	<div class="col-md-12 text-center">
		<h4><i class="fa fa-fw fa-tags"></i> Облако тегов </h4>

		{foreach from=$tags item=tag}
			<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['ukey']}" class="btn btn-default btn-xs"><span style="font-size:{$tag['fontsize']}%;" title="{$tag['title']}"><i class="fa fa-fw fa-tag fa-va"></i>{$tag['title']}</span></a>
		{/foreach}
	</div>
</div>

