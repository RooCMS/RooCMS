{* Шаблон управления помощью сайта *}
{if $smarty.const.DEBUGMODE}
	<div class="col-lg-2">
		<div class="card d-none d-lg-block submenu sticky-top">
			<div class="card-header">
				Разработчику
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=help&part=create_part" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "create_part"} active{/if}"><span class="fas fa-fw fa-plus-circle"></span> Добавить раздел</a>
			</div>
		</div>

		<div class="row justify-content-center mb-3">
			<div class="col-auto">
				<div class="btn-group btn-group-sm d-block d-lg-none">
					<a href="{$SCRIPT_NAME}?act=help&part=create_part" class="btn btn-outline-primary {if isset($smarty.get.part) && $smarty.get.part == "create_part"} active{/if}"><span class="fas fa-fw fa-plus-circle"></span> Добавить раздел</a>
				</div>
			</div>
		</div>
	</div>
{/if}
<div class="col-lg-{if $smarty.const.DEBUGMODE}10{else}12{/if}">
	<div class="card">
		{$content}
	</div>
</div>