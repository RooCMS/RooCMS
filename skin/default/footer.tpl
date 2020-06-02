{* Шаблон "ног" *}

<div class="container footer mt-3 py-3">
	<div class="row">
		<div class="col-md-6 text-dark ptsans">
			<div class="text-uppercase border-bottom pb-1 mb-2">Навигация</div>
			{if !empty($navtree)}
				<div class="d-flex flex-row flex-wrap" role="navigation">
					{foreach from=$navtree item=navitem key=k name=navigate}
						{if $smarty.foreach.navigate.first}
							<div class="d-flex flex-column col-sm-6 px-0">
						{/if}

						{if $navitem['level'] == 0 && !$smarty.foreach.navigate.first}
							</div>
							<div class="d-flex flex-column col-sm-6 px-0 mx-0">
						{/if}

						<a href="/index.php?page={$navitem['alias']}" class="text-secondary roocms-foot-link{if $navitem['level'] == 0}-first{/if}">{$navitem['title']}{if !array_key_exists($userdata['gid'], $navitem['group_access']) && $userdata['title'] == "u" && !array_key_exists(0, $navitem['group_access'])}<i class="fas fa-fw fa-lock small" rel="tooltip" data-placement="left" title="Для просмотра страницы нужны расширенные права доступа"></i>{/if}</a>

						{if $smarty.foreach.navigate.last}
							</div>
						{/if}
					{/foreach}
				</div>
			{/if}
		</div>
		<div class="col-md-{if $userdata['uid'] != 0}3{else}2{/if} col-xl-{if $userdata['uid'] != 0}4{else}2{/if} mt-3 mt-md-0 text-dark ptsans">
			<div class="text-uppercase border-bottom pb-1 mb-2">Информация</div>
			{if $config->uagreement_use}
				<a href="{$SCRIPT_NAME}?part=uagreement&ajax=true" data-fancybox data-animation-duration="300" data-type="ajax" class="text-secondary roocms-foot-link">Соглашение о передачи персональной информации</a>
			{/if}

			<div class="mt-3">{include file='counters.tpl'}</div>
		</div>
		<div class="col-md-{if $userdata['uid'] != 0}3{else}4{/if} col-xl-{if $userdata['uid'] != 0}2{else}4{/if} mt-3 mt-md-0 text-dark ptsans">
			<div class="text-uppercase border-bottom pb-1 mb-2">{if $userdata['uid'] != 0}Личный кабинет{else}Рассылка{/if}</div>
			{if $userdata['uid'] != 0}
				<a class="text-secondary roocms-foot-link" href="{$SCRIPT_NAME}?part=ucp&act=ucp"><i class="far fa-fw fa-user mr-1"></i>Личный кабинет</a>
				<br /><a class="text-secondary roocms-foot-link" href="{$SCRIPT_NAME}?part=ucp&act=pm"><i class="far fa-fw fa-envelope mr-1"></i>Личные сообщения</a>
				<br />
				{if $userdata['title'] == "a"}
					<br /><a class="text-secondary roocms-foot-link" href="{$config->cp_script}"><i class="fas fa-fw fa-skull-crossbones mr-1"></i>Admin CP</a>
					<br />
				{/if}
				<br /><a class="text-secondary roocms-foot-link" href="{$SCRIPT_NAME}?part=ucp&act=logout"><i class="fas fa-fw fa-sign-out-alt mr-1"></i>Выйти из аккаунта</a>
			{/if}
			{$module->load("express_reg")}

			<a id="move_top" href="{$smarty.server.REQUEST_URI}#" class="btn btn-secondary"><i class="fas fa-fw fa-chevron-circle-up"></i> Наверх</a>
		</div>
	</div>
	<div class="row">
		<div class="col-12 text-center small">
			<hr />
			Создано на {$copyright}
		</div>
	</div>
</div>
{*{if $smarty.const.DEBUGMODE}
	{debug}
{/if}*}

{*
{if $nitem['rss'] == 1 && $config->rss_power}
	<a href="/index.php?page={$nitem['alias']}&export=RSS" class="btn btn-sm btn-link ptsans" target="_blank" title="{$nitem['title']} RSS"><i class="fa fa-fw fa-rss"></i></a>
{/if}
*}

</body>
</html>