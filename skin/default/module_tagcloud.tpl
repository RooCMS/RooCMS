{* Шаблон для модуля: tagcloud *}
<div class="row">
	<div class="col-md-12 text-center">
		<h4> Популярные Метки </h4>

		{* If you want shuffle tag cloud use next string *}
		{*{capture}{$tags|@shuffle}{/capture}*}
		{foreach from=$tags item=tag}
			<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['ukey']}" class="btn btn-default btn-xs tag {if isset($smarty.get.tag) && $smarty.get.tag == $tag['title']}active{/if}"><span {*style="font-size:{$tag['fontsize']+30}%;"*} title="{$tag['title']}"><i class="fa fa-fw fa-tag fa-va"></i>{$tag['title']} <span class="tag_amount">{$tag['amount']}</span></span></a>
		{/foreach}
	</div>
</div>

