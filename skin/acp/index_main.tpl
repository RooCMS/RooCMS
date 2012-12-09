{* Разные предупреждения *}
{if isset($warn) && !empty($warn)}
	{foreach from=$warn item=text}
		<div class="red bold error" style="padding: 10px;margin: 5px 0px;">
			{$text}
		</div>
	{/foreach}
{/if}
<div class="option">
	<b>Текущая версия RooCMS:</b> 	{$info['roocms']} 
</div>