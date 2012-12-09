{if !isset($no_footer)}
{literal}
<style>
#copyright {
	padding: 0px; margin: 30px 0px 0px 0px;
	color: #373737;
	font-family: Ubuntu; font-size: 11px; font-weight: normal;
	background: #EDE1CC url('{/literal}{$SKIN}{literal}/img/bg_lightborder.gif') repeat-x top;
	width: 100%; height: 55px;
	border-top: 1px solid #B8AD9A;
	text-align: right;
	position: fixed; z-index: 99; bottom: 0px;
	-moz-box-shadow: 0px -1px 4px #B8AD9A; -webkit-box-shadow: 0px -1px 4px #B8AD9A; box-shadow: 0px -1px 4px #B8AD9A;}
</style>
{/literal}

	<div id="copyright">
		<noscript>
			<div class="error">
				<b>ВНИМАНИЕ!!!</b> Панель управления частично нефункциональна!
				<br />Вы используете устаревший браузер, или у вас отключена поддержка JavaScript.
				<br />Многие функции панели управления при данных условиях отключены.
				<br />Установите более современный браузер или включите поддержку JavaScript.
			</div>
		</noscript>
		<div style="float: left;padding-left: 30px;text-align: left;width: 24%;">
			{if $debug}<b class="red"><br />Внимание! У вас включен режим отладки!</b><br />{/if}
			{if $devmode}<b class="red">Внимание! У вас включен режим разработчика!</b>{/if}
		</div>
		<div style="float: right;padding-right: 10px;">
			<br />{$copyright}
			<br />Версия {$smarty.const.VERSION}
		</div>
		{if isset($db_querys) || isset($debug_time)}
		<div style="float: right;padding-right: 30px;text-align: left;">
			<br />{$db_querys}
			<br />{$debug_timer}мс
		</div>
		<div style="float: right;padding-right: 5px;">
			<br />Запросов к БД:
			<br />Время выполнения:
		</div>
		{/if}
	</div>
{/if}
</body>
</html>