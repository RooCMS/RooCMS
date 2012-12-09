{* Установленные PHP расширения *}
{foreach from=$phpextensions item=ext}
	<div class="option showol clear">
		{if $ext == "Core"
		 || $ext == "calendar"
		 || $ext == "date"
		 || $ext == "ereg"
		 || $ext == "pcre"
		 || $ext == "session"
		 || $ext == "standard"
		 || $ext == "xml"
		 || $ext == "gd"
		 || $ext == "mbstring"
		 || $ext == "SimpleXML"
		 || $ext == "apache2handler"
		 || $ext == "mysql"}
		 <font class="green bold">
		 {elseif $ext == "xdebug"}
		 <font class="dorange bold">
		 {else}
		 <font class="dgrey">
		 {/if}
		 {$ext}<span class="ui-icon ui-icon-carat-2-n-s fleft"></span>
		 </font>
		 <ol class="none">
		 {foreach from=$phpextfunc[$ext] item=extfunc}
			<li>{$extfunc}()</li>
		 {/foreach}
		 </ol>
	</div>
{/foreach}
{literal}
<script>
	$(document).ready(function(){
		$(".showol").children("font").css('cursor','pointer').click(function() {
			$(this).closest('.showol').children("ol").slideToggle();
		});
	});
</script>
{/literal}
