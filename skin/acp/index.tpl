{* Шаблон главной страницы Панели Администратора *}
<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
		{*<ul class="nav nav-list">*}
		<li class="nav-header">Системная информация</li>
		<li{if !isset($smarty.get.part)} class="active"{/if} class="bgwhite"><a href="{$SCRIPT_NAME}"><i class="fa fa-fw fa-list"></i> Сводка по сайту</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "serverinfo"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=serverinfo"><i class="fa fa-fw fa-terminal"></i> Информация о сервере</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "phpext"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=phpext"><i class="fa fa-fw fa-code"></i> PHP расширения</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "phpinfo"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=phpinfo"><i class="fa fa-fw fa-ellipsis-v"></i> PHP info</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "inivars"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=inivars"><i class="fa fa-fw fa-reorder"></i> PHP переменные</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "fileinfo"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=fileinfo"><i class="fa fa-fw fa-file"></i> Файлы и форматы</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "license"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=license"><i class="fa fa-fw fa-legal"></i> Лицензия RooCMS</a></li>
	</ul>
</div>
<div class="col-md-10">
	{$content}
</div>