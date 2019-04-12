{* Установленные PHP расширения *}
<div class="card">
	<div class="card-header">
		Установленные PHP расширения
	</div>
	<div class="card-body">
		<ul class="nav nav-pills">
			{foreach from=$phpextensions item=ext key=k}
				<li{if $k==0} class="nav-item"{/if}>
					<a data-toggle="tab" href="#{$ext}" aria-controls="{$ext}" class="nav-link {if $k==0}active{/if}
						{if $ext == "Core"
						 || $ext == "calendar"
						 || $ext == "date"
						 || $ext == "pcre"
						 || $ext == "session"
						 || $ext == "standard"
						 || $ext == "xml"
						 || $ext == "gd"
						 || $ext == "mbstring"
						 || $ext == "SimpleXML"
						 || $ext == "mysqli"}
						 text-success font-weight-bold
						{elseif $ext == "xdebug"
						     || $ext == "apache2handler"
						     || $ext == "exif"}
						 text-warning font-weight-bold
						{/if}" id="{$ext}-tab">
						{$ext}
					</a>
				</li>
			{/foreach}
		</ul>
		<div class="tab-content">
			{foreach from=$phpextensions item=ext key=k}
				<div id="{$ext}" class="tab-pane {if $k==0}active{/if}" aria-labelledby="{$ext}-tab">
					<hr />
					<h2>{$ext}</h2>
					<div class="container-fluid">
						<div class="row">
							{foreach from=$phpextfunc[$ext] item=extfunc name=func}
								<div class="col-6 col-sm-6 col-md-4 col-xl-3 border-bottom py-1">
									{if trim($extfunc) != ""}{$extfunc}(); {if $smarty.const.DEBUGMODE}<a href="https://www.php.net/manual/ru/function.{$extfunc|replace:'_':'-'}.php" target="_blank"><i class="fas fa-fw fa-external-link-alt small"></i></a>{/if}{/if}
								</div>
							{/foreach}
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
</div>
