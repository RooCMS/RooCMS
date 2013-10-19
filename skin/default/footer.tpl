{* Шаблон "ног" *}

<hr>
	<footer>
		<div class="pull-right"><small>{$copyright}</small></div>
		<img src="{$SKIN}/img/ribbon/organic_software.png" width="88" height="15" alt="100% органичический продукт" title="100% органичический продукт">
		<img src="{$SKIN}/img/ribbon/complite_firefox.png" width="88" height="15" alt="Сайт оптимизирован под Firefox" title="Сайт оптимизирован под Firefox">
		<br />

		<br />{include file='counters.tpl'}
	</footer>
</div>
{* Всякая хрень из дебага *}
{if isset($debug_info) && $debug_info != ""}
<div class="container">
	<div class="row">
    	<div class="col-xs-12">
			<div class="alert alert-dismissable t12 text-left in fade" role="alert">
		        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			    {$debug_info}
			</div>
    	</div>
	</div>
</div>
{/if}
</body>
</html>
