{* Blocks *}
<div class="col-lg-2">
	<div class="card d-none d-lg-block submenu sticky-top">
		<div class="card-header">
			Управление блоками
		</div>
		<div class="list-group">
			<a href="{$SCRIPT_NAME}?act=blocks&part=create&type=html" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "html"} active{/if}"><i class="fas fa-fw fa-cube"></i> Создать <strong>HTML</strong> блок</a>
			<a href="{$SCRIPT_NAME}?act=blocks&part=create&type=php" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "php"} active{/if}"><i class="fas fa-fw fa-cube"></i> Создать <strong>PHP</strong> блок</a>
		</div>
	</div>
	<div class="row justify-content-center mb-3">
		<div class="col-auto">
			<div class="btn-group btn-group-sm d-block d-lg-none">
				<a href="{$SCRIPT_NAME}?act=blocks&part=create&type=html" class="btn btn-outline-primary {if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "html"} active{/if}"><i class="fa fa-fw fa-cube"></i> Создать <strong>HTML</strong> блок</a>
				<a href="{$SCRIPT_NAME}?act=blocks&part=create&type=php" class="btn btn-outline-primary {if isset($smarty.get.part) && $smarty.get.part == "create" && isset($smarty.get.type) && $smarty.get.type == "php"} active{/if}"><i class="fa fa-fw fa-cube"></i> Создать <strong>PHP</strong> блок</a>
			</div>
		</div>
	</div>
</div>
<div class="col-lg-10">
	<div class="card">
		{$content}
	</div>
</div>