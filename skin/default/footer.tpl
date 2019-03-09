{* Шаблон "ног" *}
</div>
<div class="container-fluid footer">
	<div class="row">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<footer>
						<div class="row">
							<div class="col-sm-8">
								<div class="row">
									{assign var=rows value=0}
									{foreach from=$navtree item=nitem key=k name=navigate}
										{if $nitem['level'] == 0}
											{if !$smarty.foreach.navigate.first}</div>{/if}
											{assign var=rows value=$rows+1}
											{if $rows == 5}
												{assign var=rows value=1}
												</div><div class="row">
											{/if}
											<div class="col-md-3 col-xs-12 text-overflow">
											<a href="/index.php?page={$nitem['alias']}" class="btn btn-xs btn-link ptsans">{$nitem['title']}</a>
										{else}
											<br /><a href="/index.php?page={$nitem['alias']}" class="btn btn-xs btn-link ptsans">{$nitem['title']}</a>
										{/if}
										{if $smarty.foreach.navigate.last}
											</div>
										{/if}
									{/foreach}
								</div>


								{*
								{if $nitem['rss'] == 1 && $config->rss_power}
									<a href="/index.php?page={$nitem['alias']}&export=RSS" class="btn btn-xs btn-link ptsans" target="_blank" title="{$nitem['title']} RSS"><i class="fa fa-fw fa-rss"></i></a>
								{/if}
								*}

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
							</div>
						</div>
					</footer>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>