{* Шаблон просмотра помощи сайта *}
<script type="text/javascript" src="plugin/codemirror.php?mode=php"></script>

<div class="panel-heading">
	{$data['title']}
</div>

<div class="panel-body">
	{if !empty($helpmites)}
		<ul class="breadcrumb small">
			<li>
				<a href="{$SCRIPT_NAME}?act=help" rel="tooltip" title="Нажмите, что бы перейти в оглавление раздела &quot;Помощь&quot;" data-placement="top"><i class="fa fa-support fa-fw"></i></a>
			</li>
			{foreach from=$helpmites item=smites key=i name=mites}
				<li{if $smarty.foreach.mites.last} class="active"{/if}>
					{if !$smarty.foreach.mites.last}<a href="{$SCRIPT_NAME}?act=help&u={$smites['uname']}">{$smites['title']}</a>{else}{$smites['title']}{/if}
				</li>
			{/foreach}
		</ul>
	{/if}


	{if !empty($data)}
		{$data['content']}
	{else}
		<p class="lead">В данном разделе пока что нет информации.</p>
	{/if}
</div>

{if !empty($data)}
	<div class="panel-footer text-right">
		<small>Последняя редакция: {$data['date_modified']}</small>
		{if $smarty.const.DEBUGMODE}
		    <br />
		    <a href="{$SCRIPT_NAME}?act=help&part=edit_part&u={$data['uname']}" class="btn btn-xs btn-default"><i class="fa fa-pencil-square-o fa-fw"></i>Редактировать</a>
		    <a href="{$SCRIPT_NAME}?act=help&part=delete_part&u={$data['uname']}" class="btn btn-xs btn-danger"><i class="fa fa-trash-o fa-fw"></i>Удалить</a>
		{/if}
	</div>

	{if !empty($subtree)}
		<div class="panel-body">
			{foreach from=$subtree item=subpart key=k name=helptrees}
				{if $subpart['level'] == 0}
					{if $k != 0}
						</ul>
					{/if}
					<ul class="nav nav-pills nav-stacked col-lg-3 col-md-4 col-sm-4 col-xs-12">
				{/if}
				<li class="small"><a href="{$SCRIPT_NAME}?act=help&u={$subpart['uname']}" {if $subpart['level'] == 0}class="text-bold"{/if}>{section name=level loop=$subpart['level']}&bull;{/section}&bull; {$subpart['title']}</a></li>
				{if $smarty.foreach.helptrees.last}</ul>{/if}
			{/foreach}
		</div>
	{/if}
{/if}

