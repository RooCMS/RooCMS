{if !isset($no_footer)}
<div class="navbar navbar-fixed-bottom navbar-inverse hidden-xs hidden-sm" id="footer">
    <div class="col-md-3 text-left footer">
		{if $debug}<nobr><b class="text-warning t10"><span class="icon-exclamation-sign"></span> Внимание! У вас включен режим отладки!</b></nobr><br />{/if}
		{if !$debug && $devmode}<nobr><b class="text-error t10"><span class="icon-exclamation-sign"></span> ТРЕБУЕТСЯ ВКЛЮЧИТЬ РЕЖИМ ОТЛАДКИ !!!</b></nobr><br />{/if}
		{if $devmode}<nobr><b class="text-warning t10"><span class="icon-exclamation-sign"></span> Внимание! У вас включен режим разработчика!</b></nobr>{/if}
    </div>

    <div class="col-md-2 text-left footer">
        <small>
            <br /><nobr><span class="icon-bar-chart"></span> Число обращений к БД: <b>{$db_querys}</b></nobr>
        </small>
    </div>
    <div class="col-md-2 text-left footer">
        <small>
			<br /><nobr><span class="icon-dashboard"></span> Использовано памяти : <span style="cursor: help;" title="{$debug_memory} байт факт. ({round($debug_memusage/1024/1024, 2)} Мб макс)"><b>{round($debug_memory/1024/1024, 2)} Мб</b></span></nobr>
        </small>
    </div>
    <div class="col-md-2 text-left footer">
        <small>
			<br /><nobr><span class="icon-time"></span> Время работы скрипта : <b>{$debug_timer} мс</b></nobr>
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
			{if $debug}<nobr><b class="text-warning"><span class="icon-exclamation-sign"></span> Внимание! У вас включен режим отладки!</b></nobr><br />{/if}
			{if !$debug && $devmode}<nobr><b class="text-error"><span class="icon-exclamation-sign"></span> ТРЕБУЕТСЯ ВКЛЮЧИТЬ РЕЖИМ ОТЛАДКИ !!!</b></nobr><br />{/if}
			{if $devmode}<nobr><b class="text-warning"><span class="icon-exclamation-sign"></span> Внимание! У вас включен режим разработчика!</b></nobr>{/if}

			<br />
			<br /><nobr><span class="icon-bar-chart"></span> Число обращений к БД: <b>{$db_querys}</b></nobr>
			<br /><nobr><span class="icon-dashboard"></span> Использовано памяти : <span style="cursor: help;" title="{$debug_memory} байт факт. ({round($debug_memusage/1024/1024, 2)} Мб макс)"><b>{round($debug_memory/1024/1024, 2)} Мб</b></span></nobr>
			<br /><nobr><span class="icon-time"></span> Время работы скрипта : <b>{$debug_timer} мс</b></nobr>

            <br />
            <br /><nobr>{$copyright}</nobr>
			<br /><nobr>Версия {$smarty.const.ROOCMS_FULL_TEXT_VERSION}</nobr>
    	</div>
	</div>
</div>
{/if}
	</div>
</div>

{* Всякая хрень из дебага *}
{if isset($debug_info) && $debug_info != ""}
<div class="container">
	<div class="row">
    	<div class="col-xs-12">
			<div class="alert alert-dismissable t12 text-left in fade" role="alert">
		        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			    {$debug_info}
			</div>
    	</div>
	</div>
</div>
{/if}

</body>
</html>