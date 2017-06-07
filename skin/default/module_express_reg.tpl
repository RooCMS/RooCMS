{* Шаблон экспресс регистрации *}
{if !$hide}
	<hr />
	<div class="row">
		<div class="col-md-6 col-md-offset-3 text-center">
			<h2>Подпишись на новости</h2>
			Спешите получать последние новости безотлагательно

			{if $userdata['uid'] != 0}
				<br />
				<br /><a href="{$SCRIPT_NAME}?part=ucp&act=ucp&move=mailing" class="btn btn-lg btn-block btn-default"><i class="fa fa-fw fa-envelope-o"></i>Подписаться</a>
			{else}
				<form method="post" action="{$SCRIPT_NAME}?part=reg&act=expressreg">
					<br /><input type="text" class="form-control input-lg" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required>
					<br /><input type="submit" name="expressreg" class="btn btn-default" value="Подписаться">
				</form>
			{/if}
		</div>
	</div>
{/if}
