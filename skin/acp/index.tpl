{* Шаблон главной страницы Панели Администратора *}
<div class="col-md-2">
	<ul class="nav nav-pills nav-stacked">
	{*<ul class="nav nav-list">*}
		<li class="nav-header">Системная информация</li>
		<li{if !isset($smarty.get.part)} class="active"{/if}><a href="{$SCRIPT_NAME}"><span class="icon-fixed-width icon-list-ul"></span> Сводка по сайту</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "serverinfo"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=serverinfo"><span class="icon-fixed-width icon-terminal"></span> Информация о сервере</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "phpext"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=phpext"><span class="icon-fixed-width icon-code"></span> PHP расширения</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "inivars"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=inivars"><span class="icon-fixed-width icon-reorder"></span> PHP переменные</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "fileinfo"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=fileinfo"><span class="icon-fixed-width icon-file"></span> Файлы и форматы</a></li>
		<li{if isset($smarty.get.part) && $smarty.get.part == "license"} class="active"{/if}><a href="{$SCRIPT_NAME}?part=license"><span class="icon-fixed-width icon-legal"></span> Лицензия RooCMS</a></li>
	</ul>
</div>
<div class="col-md-10 thumbnail">
	<div class="caption">
    	{$content}
    </div>
</div>