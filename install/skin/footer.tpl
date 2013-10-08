{if !isset($no_footer)}

<div class="navbar navbar-fixed-bottom navbar-inverse hidden-xs hidden-sm" id="footer">
    <div class="col-md-3 text-left footer">
		<div class="progress" rel="tooltip" title="{$progress}% Завершено" data-placement="top" style="margin-top: 5px;">
		  <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%">
		    <span class="sr-only">{$progress}% Завершено</span>
		  </div>
		</div>
    </div>

    <div class="col-md-2 text-left footer">
        <small>
            {if isset($db_querys)}<br /><nobr><span class="icon-bar-chart"></span> Число обращений к БД: <b>{$db_querys}</b></nobr>{/if}
        </small>
    </div>
    <div class="col-md-2 text-left footer">
        <small>
        	{if isset($debug_memory) && isset($debug_memusage)}
				<br /><nobr><span class="icon-dashboard"></span> Использовано памяти : <span style="cursor: help;" title="{$debug_memory} байт факт. ({round($debug_memusage/1024/1024, 2)} Мб макс)"><b>{round($debug_memory/1024/1024, 2)} Мб</b></span></nobr>
			{/if}
        </small>
    </div>
    <div class="col-md-2 text-left footer">
        <small>
			 {if isset($debug_timer)}<br /><nobr><span class="icon-time"></span> Время работы скрипта : <b>{$debug_timer} мс</b></nobr>{/if}
        </small>
    </div>
    <div class="col-md-3 text-right footer">
        <small>
            <nobr>{$copyright}</nobr>
			<br /><nobr>Версия {$smarty.const.ROOCMS_FULL_TEXT_VERSION}</nobr>
		</small>
    </div>
</div>

<div class="container visible-xs visible-sm">
	<div class="row">
    	<div class="col-xs-12" style="padding-top: 20px;margin-bottom: -40px;">
			<div class="progress" rel="tooltip" title="{$progress}% Завершено" data-placement="top">
			  <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%">
			    <span class="sr-only">{$progress}% Завершено</span>
			  </div>
			</div>
			<br />
			{if isset($db_querys)}<br /><nobr><span class="icon-bar-chart"></span> Число обращений к БД: <b>{$db_querys}</b></nobr>{/if}
			{if isset($debug_memory) && isset($debug_memusage)}
				<br /><nobr><span class="icon-dashboard"></span> Использовано памяти : <span style="cursor: help;" title="{$debug_memory} байт факт. ({round($debug_memusage/1024/1024, 2)} Мб макс)"><b>{round($debug_memory/1024/1024, 2)} Мб</b></span></nobr>
			{/if}
			{if isset($debug_timer)}<br /><nobr><span class="icon-time"></span> Время работы скрипта : <b>{$debug_timer} мс</b></nobr>{/if}

            <br />
            <br /><nobr>{$copyright}</nobr>
			<br /><nobr>Версия {$smarty.const.ROOCMS_FULL_TEXT_VERSION}</nobr>
    	</div>
	</div>
</div>
{/if}
	</div>
</div>
</body>
</html>