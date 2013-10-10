<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	RSS Class
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.9
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*	RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
*
*   Это программа является свободным программным обеспечением. Вы можете
*   распространять и/или модифицировать её согласно условиям Стандартной
*   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
*   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
*
*   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
*   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
*   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
*   Общественную Лицензию GNU для получения дополнительной информации.
*
*   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
*   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//	feed/ex/im class :: RSS 2.0

$rss 	= new RSS;

class RSS {

	# param
	private		$encoding		= "utf-8";					# [string]
	private 	$version		= "2.0";					# [string]

	# header link
	public		$rss_link		= "";						# [string]

	protected	$title			= "";						# [string]
	protected	$description	= "";						# [string]
	protected	$link			= "";						# [string]
	protected 	$language		= "ru";						# [string]
	protected 	$copyright		= "";						# [string]
	protected 	$managingeditor = "";						# [string]
	protected 	$webmaster		= "";						# [string]
	private 	$generator		= "RooCMS";					# [string]
	protected 	$ttl			= 240;						# [int]
	public		$lastbuilddate	= 0;						# [int]
	protected	$image			= array("url"	=>	"",
										"title"	=>	"",
										"link"	=>	"");

	# output buffer
	protected	$items			= array();					# [array]
	public 		$out			= "";						# [text]



	//#####################################################
	// initialisation parametrs
	function init_params() {

		global $config, $site, $structure;

		$this->set_ttl($config->rss_ttl);
		$this->set_link();
		$this->set_description($structure->page_meta_desc);

		$this->managingeditor 	=& $site['sysemail'];
		$this->webmaster 		=& $site['sysemail'];
	}


	//#####################################################
	// set params
	public function set_title($title) {
		$this->title = $title;
	}


	public function set_description($text) {
		$this->description = $text;
	}


	public function set_link($url = "") {

		global $site, $parse;

		$uri = str_replace("?export=RSS", "", $parse->uri);
		$uri = str_replace("&export=RSS", "", $uri);

		if(trim($url) == "") 	$this->link = $site['domain'] . SCRIPT_NAME . htmlspecialchars($uri);
		else					$this->link	= htmlspecialchars($url);
	}


	public function set_ttl($ttl) {
		$ttl = round($ttl);
		if($ttl > 60) $this->ttl = $ttl;
	}


	public function set_lastbuilddate($date) {
		$now = time();
		if($now > $date) $this->lastbuilddate = $date;
	}


	public function set_header_link($rsslink = SCRIPT_NAME) {

		global $config, $parse;

		if(trim($parse->uri) != "") $rsslink .= $parse->uri."&export=RSS";
		else $rsslink .= "?export=RSS";

		if($config->rss_power) $this->rss_link = $parse->transform_uri($rsslink);
	}


	//#####################################################
	// draw item
	public function create_item($guid, $title, $description, $link, $pubdate, $author = "", $category = "") {

		global $site, $parse;

		$link = htmlspecialchars($site['domain']."/".$parse->transform_uri($link));
		$guid = htmlspecialchars($site['domain']."/".$parse->transform_uri($guid));

		# ??? <![CDATA[ ??? ]]>
		$item  = "\n\t\t <item>";
		$item .= "\n\t\t\t <title>".$title."</title>";
		$item .= "\n\t\t\t <description>".$description."</description>";
		$item .= "\n\t\t\t <link>".$link."</link>";
		$item .= "\n\t\t\t <comments>".$link."</comments>";
		$item .= "\n\t\t\t <pubDate>".gmdate("D, d M Y H:i:s", $pubdate)." GMT</pubDate>";
		$item .= "\n\t\t\t <pubUT>".$pubdate."</pubUT>";
		$item .= "\n\t\t\t <guid isPermaLink='true'>".$guid."</guid>";
		if(trim($category) != "") 	$item .= "\n\t\t\t <category>".$category."</category>";
		if(trim($author) != "") 	$item .= "\n\t\t\t <author>".$author."</author>";
		$item .= "\n\t\t </item>";

		$this->items[] = $item;
	}


	//#####################################################
	// draw header doc
	protected function header() {

		global $config, $parse, $site;


		$this->out .= '<?xml version="1.0" encoding="'.$this->encoding.'"?>';
		$this->out .= "\n\n";
		$this->out .= '<rss version="'.$this->version.'" xmlns:roocms="'.$site['domain'].SCRIPT_NAME.'">';
		$this->out .= "\n\t<channel>";

		# title
		if(trim($this->title) == "") $this->title =& $site['title'];
		$this->out .= "\n\t\t <title>".$this->title."</title>";

		# description
		if(trim($this->description) == "") $this->description =& $config->meta_description;
		$this->out .= "\n\t\t <description>".$this->description."</description>";

		# link
		$this->out .= "\n\t\t <link>".$this->link."</link>";

		# language
		$this->out .= "\n\t\t <language>".$this->language."</language>";

		# set email editor
		if(trim($this->managingeditor) != "") 	$this->out .= "\n\t\t <managingEditor>".$this->managingeditor."</managingEditor>";

		# set email webmaster
		if(trim($this->webmaster) != "") 		$this->out .= "\n\t\t <webMaster>".$this->webmaster."</webMaster>";

		# generator
		$this->out .= "\n\t\t <generator>".$this->generator."</generator>";

		# ttl
		$this->out .= "\n\t\t <ttl>".$this->ttl."</ttl>";

		# image
		$this->image['url'] 	= $site['domain']."/img/logo.png";
		$this->image['title'] 	= $this->title;
		$this->image['link'] 	= $this->link;
		$this->out .= "\n\t\t <image> \n\t\t\t <url>".$this->image['url']."</url> \n\t\t\t <title>".$this->image['title']."</title> \n\t\t\t <link>".$this->image['link']."</link> \n\t\t </image>";

		if($this->lastbuilddate != 0)	$this->out .= "\n\t\t <lastBuildDate>".gmdate("D, d M Y H:i:s", $this->lastbuilddate)." GMT</lastBuildDate>";
	}


	//#####################################################
	// draw footer doc
	protected function footer() {
		$this->out .= "\n\t</channel>\n</rss>";
	}


	//#####################################################
	// Output RSS
	public function out() {

		# set header type
		header("Content-type: text/xml; charset=utf-8");

		# init params
		$this->init_params();

		# init head document
		$this->header();


		# init items
		foreach($this->items AS $key=>$value) {
			$this->out .= $value;
		}


		# init footer document
		$this->footer();

		return $this->out;
	}


	//#####################################################
	// check items
	public function check_rss() {
		$c = count($this->items);
		if($c != 0) return true;
		else return false;
	}
}

?>