{* Module template: express_reg *}
{if !$hide}
	<div class="row">
		<div class="col-12 text-center">
			<h5 class="mt-0">Подпишись на новости</h5>

			{if $userdata['uid'] != 0}
				<a href="{$SCRIPT_NAME}?part=ucp&act=ucp&move=mailing" class="btn btn-block btn-primary"><i class="fas fa-fw fa-envelope"></i>Подписаться</a>
			{else}
				<form method="post" action="{$SCRIPT_NAME}?part=reg&act=expressreg">
					<input type="text" class="form-control input-sm" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
					{if $config->uagreement_use}
						<p class="small text-center mb-2">Совершая подписку Вы соглашаетесь <span class="text-nowrap">с <a href="{$SCRIPT_NAME}?part=uagreement&ajax=true" data-fancybox data-animation-duration="300" data-type="ajax"><b>условиями передачи информации</b></a></span></p>
					{/if}
					<input type="submit" name="expressreg" class="btn btn-sm btn-primary" value="Подписаться">
				</form>
			{/if}
		</div>
	</div>
{/if}
