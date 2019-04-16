{* Modulte template: search *}

<form method="post" action="{$SCRIPT_NAME}?part=search" class="form-inline mb-3">
	<div class="input-group input-group-lg w-100 border">
		<input type="search" class="form-control non-bgreq" id="InputSearch" name="search" placeholder="" minlength="{$minleight}" required>
		<div class="input-group-append">
			<button type="submit" class="btn btn-light border"><span class="d-none d-sm-inline">Искать</span> <i class="fa fa-fw fa-search"></i></button>
		</div>
	</div>
</form>