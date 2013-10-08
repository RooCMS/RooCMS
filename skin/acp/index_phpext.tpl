{* Установленные PHP расширения *}

<h3>Установленные PHP расширения</h3>

<div class="tabbable">
	<ul class="nav nav-pills">
    	{foreach from=$phpextensions item=ext key=k}
        	<li{if $k==0} class="active"{/if}><a data-toggle="tab" href="#{$ext}"
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
				 {/if}>{$ext}</a></li>
    	{/foreach}
	</ul>
	<div class="tab-content">
    	{foreach from=$phpextensions item=ext key=k}
        	<div id="{$ext}" class="tab-pane fade{if $k==0} in active{/if}">

			<table class="table table-hover table-condensed">
				{*<caption>Общая сводка</caption>*}
				<thead>
					<tr>
						<th width="33%"><span class="icon-arrow-down"></span></th>
						<th width="33%"><span class="icon-arrow-down"></span></th>
						<th width="33%"><span class="icon-arrow-down"></span></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						{foreach from=$phpextfunc[$ext] item=extfunc name=func}
						    <td>{if trim($extfunc) != ""}{$extfunc}(); <a href="http://www.php.net/manual/ru/function.{$extfunc|replace:'_':'-'}.php" target="_blank"><span class="icon-fixed-width icon-external-link small"></span></a>{/if}</td>
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
