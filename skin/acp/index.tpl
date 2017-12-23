{* Шаблон главной страницы Панели Администратора *}
<div class="col-sm-3 col-md-2">
	<div class="row hidden-xs">
		<div class="panel panel-default">

			<div class="panel-heading visible-lg">
				Системная информация
			</div>
			<div class="list-group">
				<a href="{$SCRIPT_NAME}" class="list-group-item{if !isset($smarty.get.part)} active{/if}"><i class="fa fa-fw fa-list"></i> Сводка по сайту</a>
				<a href="{$SCRIPT_NAME}?part=serverinfo" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "serverinfo"} active{/if}"><i class="fa fa-fw fa-terminal"></i> Информация о сервере</a>
				<a href="{$SCRIPT_NAME}?part=phpext" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "phpext"} active{/if}"><i class="fa fa-fw fa-code"></i> PHP расширения</a>
				<a href="{$SCRIPT_NAME}?part=phpinfo" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "phpinfo"} active{/if}"><i class="fa fa-fw fa-ellipsis-v"></i> PHP info</a>
				<a href="{$SCRIPT_NAME}?part=inivars" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "inivars"} active{/if}"><i class="fa fa-fw fa-reorder"></i> PHP переменные</a>
				<a href="{$SCRIPT_NAME}?part=fileinfo" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "fileinfo"} active{/if}"><i class="fa fa-fw fa-file"></i> Файлы и форматы</a>
				<a href="{$SCRIPT_NAME}?part=license" class="list-group-item{if isset($smarty.get.part) && $smarty.get.part == "license"} active{/if}"><i class="fa fa-fw fa-legal"></i> Лицензия RooCMS</a>
			</div>
		</div>
	</div>

	<div class="btn-group btn-group-sm btn-group-justified visible-xs">
		<a href="{$SCRIPT_NAME}" class="btn btn-default{if !isset($smarty.get.part)} active{/if}"><i class="fa fa-fw fa-list"></i></a>
		<a href="{$SCRIPT_NAME}?part=serverinfo" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "serverinfo"} active{/if}"><i class="fa fa-fw fa-terminal"></i></a>
		<a href="{$SCRIPT_NAME}?part=phpext" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "phpext"} active{/if}"><i class="fa fa-fw fa-code"></i></a>
		<a href="{$SCRIPT_NAME}?part=phpinfo" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "phpinfo"} active{/if}"><i class="fa fa-fw fa-ellipsis-v"></i></a>
		<a href="{$SCRIPT_NAME}?part=inivars" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "inivars"} active{/if}"><i class="fa fa-fw fa-reorder"></i></a>
		<a href="{$SCRIPT_NAME}?part=fileinfo" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "fileinfo"} active{/if}"><i class="fa fa-fw fa-file"></i></a>
		<a href="{$SCRIPT_NAME}?part=license" class="btn btn-default{if isset($smarty.get.part) && $smarty.get.part == "license"} active{/if}"><i class="fa fa-fw fa-legal"></i></a>
	</div>
</div>
<div class="col-sm-9 col-md-10">
	{if isset($warning_subj) && !empty($warning_subj)}
		<div class="row">
			<div class="col-sm-12">
				{foreach from=$warning_subj item=text}
					<div class="alert alert-danger">
						<i class="fa fa-fw fa-exclamation-circle"></i> {$text}
					</div>
				{/foreach}
			</div>
		</div>
	{/if}

	{$content}
</div>