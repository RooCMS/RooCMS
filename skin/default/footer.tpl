{* Шаблон "ног" *}
<div class="row">
	<div class="col-sm-12">
		{if !isset($smarty.get.part)}
			{$module->load("express_reg")}
		{/if}
		<hr />
		<footer>
			<div class="pull-right"><small>{$copyright}</small></div>
			<a id="move_top" href="{$smarty.server.REQUEST_URI}#" class="btn btn-info"><i class="fa fa-fw fa-chevron-circle-up"></i> Наверх</a>

			{include file='counters.tpl'}
		</footer>
	</div>
</div>
</div>
</body>
</html>