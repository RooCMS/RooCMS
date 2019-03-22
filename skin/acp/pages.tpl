{* Основной шаблон управления контентом страниц *}
<div class="col-lg-2">
	<div class="card d-none d-lg-block submenu sticky-top">
		<div class="card-header">
			Управление структурой
		</div>
		<div class="list-group">
			<a href="{$SCRIPT_NAME}?act=structure&part=create" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fas fa-fw fa-plus-circle"></span> Создать страницу</a>
		</div>
	</div>

	<div class="row justify-content-center mb-3">
		<div class="col-auto">
			<div class="btn-group btn-group-sm d-block d-lg-none">
				<a href="{$SCRIPT_NAME}?act=structure&part=create" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Создать страницу</a>
			</div>
		</div>
	</div>
</div>
<div class="col-lg-10">
	<div class="card">
    		{$content}
	</div>
</div>