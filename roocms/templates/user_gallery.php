<?phpclass tpl_items_user_gallery {//#########################################################//#		Основной шаблон//#########################################################function tpl_page() {$HTML = <<<HTML	<h1>Галерея изображений</h1>	<div id="content">		<table border="0" cellpadding="0" cellspacing="0">		<tr>		<td valing="top" style="vertical-align: top;width: 300px;">			<div id="gallery_category">				{html:category}			</div>		</td>		<td valign="top" style="vertical-align: top;">			{html:content}			{html:navpage}		</td>		</tr>		</table>	</div>HTML;return $HTML;}//*****************************************************// CSSfunction tpl_css() {$CSS = <<<CSS#gallery_category {padding-top: 13px;}#gallery_category b { font-family: Calibri;text-transform: uppercase; padding: 3px 0px 3px 0px;}#gallery_category li {list-style: none; line-height: 19px; width: 295px;}#gallery_category li a { color: #373737; text-decoration: underline;}#gallery_category li.sel, #gallery_category li:hover { color: #F38833; cursor: pointer; }#gallery_category li:hover a, #gallery_category li a:hover { color: #F38833; cursor: pointer; }#gallery_category li:hover, #gallery_category li.sel {background: #EDE1CC url('../img/acp/submenu_arrow.png') no-repeat 100% 50%;}#gallery_category li .items {display: none; color: #B8AD9A; font-size: 11px; padding: 0px 10px 0px 0px;margin: 0px;float: right;}#gallery_category li:hover .items, #gallery_category li.sel .items { display: inline; }.thumb {border: 1px solid #ede1cc;padding: 5px;margin: 2px;}CSS;return $CSS;}//*****************************************************// JSfunction tpl_js() {$JS = <<<JSJS;return $JS;}//#########################################################//#		Элементы шаблона//#########################################################//***************************// 	{html:content}//	Главная страницаfunction main() {$HTML = <<<HTML	<h2 style="padding-top: 10px;">Новые изображения:</h2>	{html:images}HTML;return $HTML;}//***************************// 	{html:content}//	Просмотр категорииfunction show_category($category) {$HTML = <<<HTML	<h2 style="padding-top: 10px;">{$category['name']}</h2>	{html:images}HTML;return $HTML;}//***************************// 	{html:images}//	Изображенияfunction show_images($image) {$HTML = <<<HTML	<a href="upload/gallery/{$image['original_img']}" title="{$image['description']}" align="left" rel="img"><img src="upload/gallery/{$image['thumb_img']}" border="0" alt="{$image['description']}" class="thumb"></a>HTML;return $HTML;}//***************************// 	{html:category}//	Отрисовываем категорииfunction category($category, $sel_cat_id=0) {$f = "ий"; if($category['images'] == 1) $f = "ие"; elseif($category['images'] == 2 OR $category['images'] == 3 OR $category['images'] == 4) $f = "ия";if($sel_cat_id != 0 && $sel_cat_id == $category['cat_id']) $class = " class=\"sel\"";else $class="";if($category['type'] == "category") {$HTML = <<<HTML	<li{$class}>		<a href="{THIS}?category={$category['cat_id']}" style="padding-left: {$category['padding']}px">{$category['cat_name']}</a> <font class="items">{$category['images']} изображен{$f}</font>	</li>HTML;}else {$HTML = <<<HTML	<b style="padding-left: {$category['padding']}px">{$category['cat_name']}</b><br />HTML;}return $HTML;}//***********************************************// 	{html:navpage} //	Отрисовка навигации по страницамfunction navpage() {$HTML = <<<HTML	<div style="border-top: 3px dotted #B8AD9A;margin-top: 30px;padding-top: 10px;">Страница: {html:navpage_el}</div>HTML;return $HTML;}//***********************************************// 	{html:navpage_el} //	Элмент навигации по страницам (ссылка на страницу)function navpage_el($cat_id, $page, $part="category") {$topage = ($page != 1) ? "&page=".$page : "" ;$HTML = <<<HTML	<b><a href="{THIS}?{$part}={$cat_id}{$topage}" class="linkb">{$page}</a></b>HTML;return $HTML;}// end class}?>