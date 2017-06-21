{* Шаблон моделя поиска *}

<form method="post" action="{$SCRIPT_NAME}?part=search" class="form-inline pull-right" role="form">
	<div class="form-group">
		<label for="InputSearch">Искать</label>
		<input type="search" class="form-control input-sm non-bgreq" id="InputSearch" name="search" placeholder="" minlength="{$minleight}" required>
	</div>
	<button type="submit" class="btn btn-default btn-sm"><i class="fa fa-fw fa-search"></i></button>
</form>