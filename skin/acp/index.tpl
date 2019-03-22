{* Шаблон главной страницы Панели Администратора *}
<div class="col-md-2">
	<div class="card d-none d-md-block submenu sticky-top">
		<div class="card-header">
			Системная информация
		</div>
		<div class="list-group">
			<a href="{$SCRIPT_NAME}" class="list-group-item list-group-item-action text-decoration-none{if !isset($smarty.get.part)} active{/if}"><i class="far fa-fw fa-list-alt"></i> Сводка по сайту</a>
			<a href="{$SCRIPT_NAME}?part=serverinfo" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "serverinfo"} active{/if}"><i class="fas fa-fw fa-server"></i> Информация о сервере</a>
			<a href="{$SCRIPT_NAME}?part=phpext" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "phpext"} active{/if}"><i class="fas fa-fw fa-code"></i> PHP расширения</a>
			<a href="{$SCRIPT_NAME}?part=phpinfo" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "phpinfo"} active{/if}"><i class="fab fa-fw fa-php"></i> PHP info</a>
			<a href="{$SCRIPT_NAME}?part=inivars" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "inivars"} active{/if}"><i class="fas fa-fw fa-laptop-code"></i> PHP переменные</a>
			<a href="{$SCRIPT_NAME}?part=fileinfo" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "fileinfo"} active{/if}"><i class="far fa-fw fa-file-alt"></i> Файлы и форматы</a>
			<a href="{$SCRIPT_NAME}?part=license" class="list-group-item list-group-item-action text-decoration-none{if isset($smarty.get.part) && $smarty.get.part == "license"} active{/if}"><i class="fab fa-fw fa-leanpub"></i> Лицензия RooCMS</a>
		</div>
	</div>
	<div class="row justify-content-center mb-3">
		<div class="col-auto">
			<div class="btn-group btn-group-sm d-block d-md-none">
				<a href="{$SCRIPT_NAME}" class="btn btn-outline-primary{if !isset($smarty.get.part)} active{/if}"><i class="far fa-fw fa-list-alt"></i></a>
				<a href="{$SCRIPT_NAME}?part=serverinfo" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "serverinfo"} active{/if}"><i class="fas fa-fw fa-server"></i></a>
				<a href="{$SCRIPT_NAME}?part=phpext" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "phpext"} active{/if}"><i class="fas fa-fw fa-code"></i></a>
				<a href="{$SCRIPT_NAME}?part=phpinfo" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "phpinfo"} active{/if}"><i class="fab fa-fw fa-php"></i></a>
				<a href="{$SCRIPT_NAME}?part=inivars" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "inivars"} active{/if}"><i class="fas fa-fw fa-laptop-code"></i></a>
				<a href="{$SCRIPT_NAME}?part=fileinfo" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "fileinfo"} active{/if}"><i class="far fa-fw fa-file-alt"></i></a>
				<a href="{$SCRIPT_NAME}?part=license" class="btn btn-outline-primary{if isset($smarty.get.part) && $smarty.get.part == "license"} active{/if}"><i class="fab fa-fw fa-leanpub"></i></a>
			</div>
		</div>
	</div>

</div>
<div class="col-md-10">
	{if isset($warning_subj) && !empty($warning_subj)}
		<div class="row">
			<div class="col-12">
				{foreach from=$warning_subj item=text}
					<div class="alert alert-danger">
						<i class="fas fa-fw fa-exclamation-circle"></i> {$text}
					</div>
				{/foreach}
			</div>
		</div>
	{/if}

	{$content}
</div>