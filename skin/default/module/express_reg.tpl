{* Module template: express_reg *}
{if !$hide}
	<div class="row">
		<div class="col-xs-12 text-center">
			<h4 style="margin-top: 0;">Подпишись на новости</h4>

			{if $userdata['uid'] != 0}
				<a href="{$SCRIPT_NAME}?part=ucp&act=ucp&move=mailing" class="btn btn-sm btn-block btn-default"><i class="fa fa-fw fa-envelope-o"></i>Подписаться</a>
			{else}
				<form method="post" action="{$SCRIPT_NAME}?part=reg&act=expressreg">
					<input type="text" class="form-control input-sm" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
					{if isset($config->fl152_use) && $config->fl152_use}
						<p class="small text-center">Совершая подписку Вы соглашаетесь <nobr>с <a href="{$SCRIPT_NAME}?part=fl152&ajax=true" data-fancybox data-animation-duration="300" data-type="ajax"><b>условиями передачи информации</b></a></nobr></p>
					{/if}
					<input type="submit" name="expressreg" class="btn btn-sm btn-default" value="Подписаться">
				</form>
			{/if}
		</div>
	</div>
{/if}
