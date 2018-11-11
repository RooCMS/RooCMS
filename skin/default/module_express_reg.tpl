{* Шаблон экспресс регистрации *}
{if !$hide}
	<div class="row">
		<div class="col-xs-12">
			<hr />
		</div>
		<div class="col-md-6 col-md-offset-3 text-center">
			<h2>Подпишись на новости</h2>
			Спешите получать последние новости безотлагательно

			{if $userdata['uid'] != 0}
				<br />
				<br /><a href="{$SCRIPT_NAME}?part=ucp&act=ucp&move=mailing" class="btn btn-lg btn-block btn-default"><i class="fa fa-fw fa-envelope-o"></i>Подписаться</a>
			{else}
				<form method="post" action="{$SCRIPT_NAME}?part=reg&act=expressreg">
					<br /><input type="text" class="form-control input-lg" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
					{if isset($config->fl152_use) && $config->fl152_use}
						<p class="small text-center">Совершая подписку Вы соглашаетесь <nobr>с <a href="{$SCRIPT_NAME}?part=fl152&ajax=true" rel="html"><b>условиями передачи информации</b></a></nobr></p>
					{/if}
					<br /><input type="submit" name="expressreg" class="btn btn-default" value="Подписаться">
				</form>
			{/if}
		</div>
	</div>
{/if}
