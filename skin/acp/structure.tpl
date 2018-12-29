{* Шаблон управления структурой сайта *}
<div class="col-sm-3 col-md-2">
	<div class="row hidden-xs">
		<div class="panel panel-default">

			<div class="panel-heading visible-lg">
				Управление структурой сайта
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}?act=structure&part=create" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Создать страницу</a>
			</div>
		</div>
	</div>
	{if !isset($smarty.get.part)}
	<div class="row hidden-xs text-center">
		<p class="text-primary text-bold">Показать только:</p>
		<div class="btn-group roocms-boolui nav-onoff" data-toggle="buttons">
			<label class="btn btn-xs btn-default">
				<input type="checkbox" name="showoptions" value="0" id="show_onlynav" checked><span class="text-info"><i class="fa fa-fw fa-check-square-o"></i>Навигацию</span>
			</label>
		</div>
	</div>
	{/if}

	<div class="btn-group btn-group-sm btn-group-justified visible-xs submenu-xs">
		<a href="{$SCRIPT_NAME}?act=structure&part=create" class="btn btn-default {if isset($smarty.get.part) && $smarty.get.part == "create"} active{/if}"><span class="fa fa-fw fa-plus-circle"></span> Создать страницу</a>
	</div>
</div>
<div class="col-sm-9 col-md-10">
	<div class="panel panel-default">
		{$content}
	</div>
</div>