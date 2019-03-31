{* Шаблон просмотра помощи сайта *}
<script type="text/javascript" src="plugin/codemirror.php?mode=php"></script>

<div class="card-header">
	{$data['title']}
</div>

<div class="card-body">
	{if !empty($helpmites)}
		<ol class="breadcrumb small">
			<li class="breadcrumb-item">
				<a href="{$SCRIPT_NAME}?act=help" rel="tooltip" title="Нажмите, что бы перейти в оглавление раздела &quot;Помощь&quot;" data-placement="top"><i class="fas fa-life-ring fa-fw"></i>Помощь</a>
			</li>
			{foreach from=$helpmites item=smites key=i name=mites}
				<li class="breadcrumb-item{if $smarty.foreach.mites.last} active{/if}">
					{if !$smarty.foreach.mites.last}<a href="{$SCRIPT_NAME}?act=help&u={$smites['uname']}">{$smites['title']}</a>{else}{$smites['title']}{/if}
				</li>
			{/foreach}
		</ol>
	{/if}


	{if !empty($data)}
		{if $data['id'] != 1}<h2>{$data['title']}</h2>{/if}
		{$data['content']}
	{else}
		<p class="lead">В данном разделе пока что нет информации.</p>
	{/if}
</div>

{if !empty($data)}
	<div class="card-footer text-right">
		<small>Последняя редакция: {$data['date_modified']}</small>
		{if $smarty.const.DEBUGMODE}
		    <br />
		    <a href="{$SCRIPT_NAME}?act=help&part=edit_part&u={$data['uname']}" class="btn btn-sm btn-primary"><i class="far fa-edit fa-fw"></i>Редактировать</a>
		    <a href="{$SCRIPT_NAME}?act=help&part=delete_part&u={$data['uname']}" class="btn btn-sm btn-danger"><i class="far fa-trash-alt fa-fw"></i>Удалить</a>
		{/if}
	</div>

	{if !empty($subtree)}
		<div class="card-body">
			{foreach from=$subtree item=subpart key=k name=helptrees}
				{if $subpart['level'] == 0}
					{if $k != 0}
						</ul>
					{/if}
					<ul class="list-group d-inline-flex col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-3 mr-n2 ml-1">
				{/if}
				<a href="{$SCRIPT_NAME}?act=help&u={$subpart['uname']}" class="list-group-item list-group-item-action{if $subpart['level'] == 0} font-weight-bold{/if}">{section name=level loop=$subpart['level']}&bull;{/section} {$subpart['title']}</a>
				{if $smarty.foreach.helptrees.last}</ul>{/if}
			{/foreach}
		</div>
	{/if}
{/if}

