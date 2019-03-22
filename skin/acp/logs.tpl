{* Log Main Template *}
<div class="col-lg-2">
	<div class="card d-none d-lg-block submenu sticky-top">
		<div class="card-header">
			Управление структурой
		</div>
		<div class="list-group">
			<a href="{$SCRIPT_NAME}?act=logs&part=logaction" class="list-group-item list-group-item-action text-decoration-none{if !isset($smarty.get.part) || (isset($smarty.get.part) && $smarty.get.part == "logaction")} active{/if}"><span class="far fa-fw fa-list-alt"></span> Лог действий</a>
			<a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "lowerrors"} active{/if}"><span class="fas fa-fw fa-list-alt"></span> Ошибки PHP</a>
			<a href="{$SCRIPT_NAME}?act=logs&part=syserrors" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "syserrors"} active{/if}"><span class="fas fa-fw fa-exclamation-triangle"></span> Критические ошибки</a>
		</div>
	</div>

	<div class="row justify-content-center mb-3">
		<div class="col-auto">
			<div class="btn-group btn-group-sm d-block d-lg-none">
				<a href="{$SCRIPT_NAME}?act=logs&part=logaction" class="btn btn-outline-primary {if !isset($smarty.get.part) || (isset($smarty.get.part) && $smarty.get.part == "logaction")} active{/if}"><span class="far fa-fw fa-list-alt"></span>Лог</a>
				<a href="{$SCRIPT_NAME}?act=logs&part=lowerrors" class="btn btn-outline-primary {if isset($smarty.get.part) && $smarty.get.part == "lowerrors"} active{/if}"><span class="fas fa-fw fa-list-alt"></span>PHP</a>
				<a href="{$SCRIPT_NAME}?act=logs&part=syserrors" class="btn btn-outline-primary {if isset($smarty.get.part) && $smarty.get.part == "syserrors"} active{/if}"><span class="fas fa-fw fa-exclamation-triangle"></span>Крит.ошибки</a>
			</div>
		</div>
	</div>

	<div class="btn-group btn-group-sm btn-group-justified visible-xs submenu-xs">

	</div>
</div>
<div class="col-lg-10">
	<div class="card">
		{$content}
	</div>
</div>