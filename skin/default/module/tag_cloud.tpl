{* Module template: tag_cloud *}
{* If you want shuffle tag cloud use next string *}
{*{capture}{$tags|@shuffle}{/capture}*}
{foreach from=$tags item=tag}
	<a href="{$SCRIPT_NAME}?part=tags&tag={$tag['ukey']}" class="tag text-monospace btn btn-outline-dark btn-sm text-capitalize mb-1 {if isset($smarty.get.tag) && $smarty.get.tag == $tag['title']}active{/if}"><span {*style="font-size:{$tag['fontsize']+30}%;"*} title="{$tag['title']}">{*<i class="fa fa-fw fa-tag fa-va"></i>*} {$tag['title']} <span class="tag_amount">{$tag['amount']}</span></span></a>
{/foreach}


