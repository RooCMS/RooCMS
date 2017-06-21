{* Шаблон для активации аккаунта *}

<h1>Активация аккаунта</h1>

<div class="row">
	<div class="col-sm-12">
		<form method="post" action="{$SCRIPT_NAME}?part=reg&act=verification" role="form" class="form-horizontal">
			<hr />
			<div class="form-group">
				<label for="inputEmail" class="col-lg-4 control-label">
					Электронная почта:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Нельзя заводить несколько аккаунтов на один почтовый ящик. После регистрации на почту будет отправлен код подтверждения." data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="email" id="inputEmail" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{literal}{2,6}{/literal}$" required value="{$email}">
				</div>
			</div>

			<div class="form-group">
				<label for="inputActivationCode" class="col-lg-4 control-label">
					Код активации:  <small><span class="fa fa-question-circle fa-fw" rel="tooltip" title="Код активации аккаунта" data-placement="left"></span></small>
				</label>
				<div class="col-lg-8">
					<input type="text" name="code" id="inputActivationCode" class="form-control"  pattern="^[\d\D]{literal}{5,}{/literal}" required value="{$code}">
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8 col-md-offset-4">
					<input type="submit" name="activate" class="btn btn-success btn-sm" value="Активировать аккаунт">
				</div>
			</div>
		</form>
	</div>
</div>