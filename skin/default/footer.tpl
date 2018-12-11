{* Шаблон "ног" *}
<div class="row">
	<div class="col-xs-12">
		<hr />
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<footer>
			<div class="row">
				<div class="col-sm-8">
					<div class="row">
						{foreach from=$navtree item=nitem key=k name=fnavigate}
							<div class="col-sm-6 col-md-4 col-lg-3 text-left">
								<a href="/index.php?page={$nitem['alias']}" class="btn btn-xs btn-link ptsans">{$nitem['title']}</a>
								{if $nitem['rss'] == 1}
									<a href="/index.php?page={$nitem['alias']}&export=RSS" class="btn btn-xs btn-link ptsans" target="_blank" title="{$nitem['title']} RSS"><i class="fa fa-fw fa-rss"></i></a>
								{/if}
							</div>
						{/foreach}
					</div>
				</div>
				<div class="col-sm-4">
					{if !isset($smarty.get.part)}
						{$module->load("express_reg")}
					{/if}
				</div>
			</div>
			<div class="row">
				<div class="col-sm-8">
					{include file='counters.tpl'}
				</div>
				<div class="col-sm-4">
					<div class="pull-right"><small>{$copyright}</small></div>
					<a id="move_top" href="{$smarty.server.REQUEST_URI}#" class="btn btn-info"><i class="fa fa-fw fa-chevron-circle-up"></i> Наверх</a>

					{include file='counters.tpl'}
				</div>
			</div>
		</footer>
	</div>
</div>
</div>
</body>
</html>