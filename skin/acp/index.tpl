{* Шаблон главной страницы Панели Администратора *}
<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
	{*<ul class="nav nav-list">*}
		<li class="nav-header">Системная информация</li>
		<li{if !isset($smarty.get.part)} class="active"{/if}><a href="{$SCRIPT_NAME}"><span class="fa fa-fw fa-list"></span> Сводка по сайту</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "serverinfo"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=serverinfo"><span class="fa fa-fw fa-terminal"></span> Информация о сервере</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "phpext"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=phpext"><span class="fa fa-fw fa-code"></span> PHP расширения</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "phpinfo"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=phpinfo"><span class="fa fa-fw fa-ellipsis-vertical"></span> PHP info</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "inivars"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=inivars"><span class="fa fa-fw fa-reorder"></span> PHP переменные</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "fileinfo"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=fileinfo"><span class="fa fa-fw fa-file"></span> Файлы и форматы</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "license"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=license"><span class="fa fa-fw fa-legal"></span> Лицензия RooCMS</a></li>
	</ul>
</div>
<div class="col-md-10 thumbnail">
	<div class="caption">
    	{$content}
    </div>
</div>