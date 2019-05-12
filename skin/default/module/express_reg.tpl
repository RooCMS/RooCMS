{* Module template: express_reg *}
{if !$hide}
	<div class="row">
		<div class="col-12 text-center">
			<h5 class="mt-0">Подпишись на новости</h5>

			{if $userdata['uid'] != 0}
				<a href="{$SCRIPT_NAME}?part=ucp&act=ucp&move=mailing" class="btn btn-block btn-primary"><i class="fas fa-fw fa-envelope"></i>Подписаться</a>
			{else}
				<form method="post" action="{$SCRIPT_NAME}?part=reg&act=expressreg">
					<input type="text" class="form-control input-sm" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" id="ExpressMailing" required>
					{if $config->uagreement_use}
						<p class="small text-center mb-2">Совершая подписку Вы соглашаетесь <span class="text-nowrap">с <a href="{$SCRIPT_NAME}?part=uagreement&ajax=true" data-fancybox data-animation-duration="300" data-type="ajax"><b>условиями передачи информации</b></a></span></p>
					{/if}
					{if $config->captcha_power}
						<div class="row mb-3 collapse" id="captchaMailing">
							<div class="col-xl-6 text-center d-flex flex-row justify-content-center align-items-center">
								<img src="/captcha.php" alt="Код для защиты от СПАМа" class="CaptchaCode">
								<div class="d-flex flex-column">
									<a href="#" class="badge badge-light ml-1 refresh-CaptchaCode" tabindex="-1" title="Обновить изображение"><i class="fas fa-fw fa-redo-alt"></i></a>
									<a href="#" class="badge badge-light ml-1 mt-1 recycle-CaptchaCode" tabindex="-1" title="Сменить код"><i class="fas fa-fw fa-recycle"></i></a>
									<a href="/captcha.php" class="badge badge-light ml-1 mt-1 zoom-CaptchaCode" tabindex="-1" data-fancybox="gallery_captcha" data-width="360" data-height="170" title="Увеличить изображение"><i class="fas fa-fw fa-search-plus"></i></a>
								</div>
							</div>
							<div class="col-xl-6">
								<div class="form-group">
									<label for="inputCaptcha">
										Защитный код: <i class="fas fa-question-circle fa-fw" rel="tooltip" title="Из-за множества программ для СПАМа и другого вредоносного софта, мы просим Вас пройти простую проверку, доказывающую, что за компьютером сидит человек..." data-placement="top"></i></small>
									</label>
									<input type="text" name="captcha" id="inputCaptcha" class="form-control" aria-describedby="captchaHelp" placeholder="" required>
									<small id="captchaHelp" class="form-text text-muted">Введите код с картинки (буквы и цифры), что бы помочь нам защититься от СПАМа</small>
								</div>
							</div>
						</div>
					{/if}
					<input type="submit" name="expressreg" class="btn btn-sm btn-primary" value="Подписаться">
				</form>
			{/if}
		</div>
	</div>
{/if}
