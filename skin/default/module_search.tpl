{* Шаблон моделя поиска *}

<form class="form-inline pull-right" method="post" action="{$SCRIPT_NAME}?part=search">
	<div class="form-group">
		<label for="InputSearch">Искать</label>
		<input type="search" class="form-control input-sm" id="InputSearch" placeholder="" minlength="{$minleight}">
	</div>
	<button type="submit" class="btn btn-default btn-sm"><i class="fa fa-fw fa-search"></i></button>
</form>