{* Шаблон "ног" *}
<hr />
<div class="row">
	<div class="col-sm-6 col-sm-offset-6 text-right">
		<button class="btn btn-default btn-xs" rel="popover" data-toggle="popover" data-html="true" data-content="<img src='qrcode.php?url={$smarty.server.REQUEST_URI}'>" data-placement="top"><i class="fa fa-fw fa-qrcode"></i>qrcode</button>
	</div>
</div>
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
