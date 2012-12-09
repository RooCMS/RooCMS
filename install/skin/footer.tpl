{if !isset($no_footer)}
{literal}
<style>
#copyright {
	padding: 0px; margin: 30px 0px 0px 0px;
	color: #373737;
	font-family: Ubuntu; font-size: 11px; font-weight: normal;
	background: #EDE1CC url('skin/acp/img/bg_lightborder.gif') repeat-x top;
	width: 100%; height: 55px;
	border-top: 1px solid #B8AD9A;
	text-align: right;
	position: fixed; z-index: 99; bottom: 0px;
	-moz-box-shadow: 0px -1px 4px #B8AD9A; -webkit-box-shadow: 0px -1px 4px #B8AD9A; box-shadow: 0px -1px 4px #B8AD9A;}
	#progressbar {margin-top: 18px;}
</style>
<script>
$(document).ready(function() {
	$( "#progressbar" ).progressbar({
		value: {/literal}{$progress}{literal},
		disabled: false
	});

	/*$("tr.option").css({'opacity': '0'}).hide();
	var time = 25;
	$.each($("tr.option"),function() {
		$(this).show().delay(time).animate({opacity: '1'});
		time += 25;
	});*/
});
</script>
{/literal}

	<div id="copyright">
		<noscript>
			<div class="error">
				<b>ВНИМАНИЕ!!!</b>
				<br />Вы используете устаревший браузер, или у вас отключена поддержка JavaScript.
				<br />Многие функции при данных условиях отключены.
				<br />Установите более современный браузер или включите поддержку JavaScript.
			</div>
		</noscript>
		<div style="float: left;padding-left: 30px;text-align: left;width: 24%;">
			<div id="progressbar"></div>
		</div>
		<div style="float: left;padding-left: 30px;text-align: left;">
			{if trim($status) != ""}
				<br />{$status}
			{/if}
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