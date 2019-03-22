{* Template Structure *}
<div class="col-lg-2">
	<div class="card d-none d-lg-block submenu sticky-top">
		<div class="card-header">
			Управление структурой
		</div>
		<div class="list-group">
			<a href="{$SCRIPT_NAME}?act=structure&part=create" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fas fa-fw fa-plus-circle"></span> Создать страницу</a>
		</div>
		{if !isset($smarty.get.part)}
		<div class="card-footer d-none d-lg-block text-center">
			<div class="custom-control custom-switch">
				<input type="checkbox" name="showoptions" class="custom-control-input nav-onoff" value="0" id="show_onlynav" >
				<label class="custom-control-label" for="show_onlynav">Показывать только навигацию</label>
			</div>
		</div>
		{/if}
	</div>

	<div class="row justify-content-center mb-3">
		<div class="col-12 text-center">
			<div class="btn-group btn-group-sm d-block d-lg-none">
				<a href="{$SCRIPT_NAME}?act=structure&part=create" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Создать страницу</a>
			</div>
			{if !isset($smarty.get.part)}
				<div class="col-12 d-lg-none text-center mt-3">
					<div class="custom-control custom-switch">
						<input type="checkbox" name="showoptions" class="custom-control-input nav-onoff" value="0" id="show_onlynav2" >
						<label class="custom-control-label" for="show_onlynav2">Показывать только навигацию</label>
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>
<div class="col-lg-10">
	<div class="card">
		{$content}
	</div>
</div>