{* Установленные PHP расширения *}

<div class="panel-heading">
	{$part_title}
</div>

<div class="panel-body">
	<div class="tabbable">
		<ul class="nav nav-pills">
			{foreach from=$phpextensions item=ext key=k}
				<li{if $k==0} class="active"{/if}>
					<a data-toggle="tab" href="#{$ext}"
						{if $ext == "Core"
						 || $ext == "calendar"
						 || $ext == "date"
						 || $ext == "ereg"
						 || $ext == "pcre"
						 || $ext == "session"
						 || $ext == "standard"
						 || $ext == "xml"
						 || $ext == "gd"
						 || $ext == "mbstring"
						 || $ext == "SimpleXML"
						 || $ext == "apache2handler"
						 || $ext == "mysql"}
							class="text-success"
						{elseif $ext == "xdebug"}
							class="text-warning"
						{/if}>
					{$ext}</a>
				</li>
			{/foreach}
		</ul>
		<div class="tab-content">
			{foreach from=$phpextensions item=ext key=k}
				<div id="{$ext}" class="tab-pane fade{if $k==0} in active{/if}">

					<table class="table table-hover table-condensed">
						{*<caption>Общая сводка</caption>*}
						<thead>
							<tr>
								<th width="33%"><span class="fa fa-caret-down"></span></th>
								<th width="33%"><span class="fa fa-caret-down"></span></th>
								<th width="33%"><span class="fa fa-caret-down"></span></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								{foreach from=$phpextfunc[$ext] item=extfunc name=func}
								    <td>{if trim($extfunc) != ""}{$extfunc}(); {if $smarty.const.DEVMODE || $smarty.const.DEBUGMODE}<a href="http://www.php.net/manual/ru/function.{$extfunc|replace:'_':'-'}.php" target="_blank"><span class="fa fa-fw fa-external-link small"></span></a>{/if}{/if}</td>
									{if $smarty.foreach.func.index % 3 == 2}
							</tr>
							<tr>
									{/if}
								{/foreach}
							</tr>
						</tbody>
					</table>


				</div>
			{/foreach}
		</div>
	</div>
</div>
