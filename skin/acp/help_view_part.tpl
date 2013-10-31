{* Шаблон просмотра помощи сайта *}

<h3>{$data['title']}</h3>
{if !empty($helpmites)}
	<ul class="breadcrumb small">
        <li>
            <a href="{$SCRIPT_NAME}?act=help" rel="tooltip" title="Нажмите, что бы перейти в оглавление раздела &quot;Помощь&quot;" data-placement="top"><span class="fa fa-medkit fa-fw"></span></a>
        </li>
        {foreach from=$helpmites item=smites key=i name=mites}
            <li{if $smarty.foreach.mites.last} class="active"{/if}>
            	{if !$smarty.foreach.mites.last}<a href="{$SCRIPT_NAME}?act=help&u={$smites['uname']}">{$smites['title']}</a>{else}{$smites['title']}{/if}
            </li>
        {/foreach}
	</ul>
{/if}
<div class="row">
	<div class="col-md-12">
		{if !empty($data)}
            {$data['content']}
		{else}
			<p class="lead">В данном разделе пока что нет информации.</p>
		{/if}
	</div>
	{if !empty($data)}
	<div class="col-md-12 text-right">
    	<small>Последняя редакция: {$data['date_modified']}</small>
        {if $smarty.const.DEVMODE}
            <br />
            <a href="{$SCRIPT_NAME}?act=help&part=edit_part&u={$data['uname']}" class="btn btn-xs btn-default"><span class="fa fa-pencil-square-o fa-fw"></span>Редактировать</a>
            <a href="{$SCRIPT_NAME}?act=help&part=delete_part&u={$data['uname']}" class="btn btn-xs btn-danger"><span class="fa fa-trash-o fa-fw"></span>Удалить</a>
        {/if}
	</div>
	{if !empty($subtree)}<hr>{/if}
	<div class="col-md-12">
		{foreach from=$subtree item=subpart key=k name=helptrees}
			{if $subpart['level'] == 0}
				{if $k != 0}
					</ul>
				{/if}
            	<ul class="nav nav-pills nav-stacked col-lg-3 col-md-4 col-sm-4 col-xs-12">

			{/if}
            <li class="small"><a href="{$SCRIPT_NAME}?act=help&u={$subpart['uname']}">{section name=level loop=$subpart['level']}&bull;{/section}&bull; {$subpart['title']}</a></li>
            {if $smarty.foreach.helptrees.last}</ul>{/if}
		{/foreach}
	</div>
	{/if}
</div>
