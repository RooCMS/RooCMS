<?php

class tpl_items_user_footer {

//#########################################################
//#		Основной шаблон
//#########################################################
function tpl_page() {
global $var;
$HTML = <<<HTML
	<div id="copyright">

	<img src="img/ribbon/organic_software.png" width="88" height="15" alt="100% органичический продукт" title="100% органичический продукт" class="img">
	<img src="img/ribbon/complite_firefox.png" width="88" height="15" alt="Сайт оптимизирован под Firefox" title="Сайт оптимизирован под Firefox" class="img">
	<br />

	<br />
	
	<!--Openstat-->
	<!--
	<span id="openstat2206821"></span>
	<script type="text/javascript">
	var openstat = { counter: 2206821, image: 5081, color: "fffffb", next: openstat, track_links: "all" };
	(function(d, t, p) {
	var j = d.createElement(t); j.async = true; j.type = "text/javascript";
	j.src = ("https:" == p ? "https:" : "http:") + "//openstat.net/cnt.js";
	var s = d.getElementsByTagName(t)[0]; s.parentNode.insertBefore(j, s);
	})(document, "script", document.location.protocol);
	</script>
	-->
	<!--/Openstat-->


	<!--LiveInternet counter-->
	<!--
	<script type="text/javascript">
	document.write("<a href='http://www.liveinternet.ru/click' "+
	"target=_blank rel=nofollow><img src='//counter.yadro.ru/hit?t15.6;r"+
	escape(document.referrer)+((typeof(screen)=="undefined")?"":
	";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
	screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
	";"+Math.random()+
	"' alt='' title='LiveInternet: показано число просмотров за 24"+
	" часа, посетителей за 24 часа и за сегодня' "+
	"border='0' width='88' height='31'><\/a>")
	</script>
	-->
	<!--/LiveInternet-->
	
	
	<!-- Yandex.Metrika informer -->
	<!--
	<a href="http://metrika.yandex.ru/stat/?id=10076272&amp;from=informer"
	target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/10076272/3_0_F7F2E5FF_F7F2E5FF_0_pageviews"
	style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" onclick="try{Ya.Metrika.informer({i:this,id:10076272,type:0,lang:'ru'});return false}catch(e){}"/></a>

	<div style="display:none;"><script type="text/javascript">
	(function(w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				w.yaCounter10076272 = new Ya.Metrika({id:10076272, enableAll: true, trackHash:true});
			}
			catch(e) { }
		});
	})(window, "yandex_metrika_callbacks");
	</script></div>
	<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script>
	<noscript><div><img src="//mc.yandex.ru/watch/10076272" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	-->
	<!-- /Yandex.Metrika counter -->
	
	<br />{copyright}</div>
	
	</div>
	</center>
	
	</body>
	</html>
HTML;
return $HTML;
}

//*****************************************************
// CSS
function tpl_css() {
$CSS = <<<CSS

CSS;
return $CSS;
}

//*****************************************************
// JS
function tpl_js() {
$JS = <<<JS

JS;
return $JS;
}

//#########################################################
//#		Элементы шаблона
//#########################################################

}

?>