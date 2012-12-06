<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS RSS 2.0 Class
|	Author:	alex Roosso
|	Copyright: 2010-2011 (c) RooCMS. 
|	Web: http://www.roocms.com
|	All rights reserved.
|----------------------------------------------------------
|	This program is free software; you can redistribute it and/or modify
|	it under the terms of the GNU General Public License as published by
|	the Free Software Foundation; either version 2 of the License, or
|	(at your option) any later version.
|	
|	Данное программное обеспечение является свободным и распространяется
|	по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
|	При любом использовании данного ПО вы должны соблюдать все условия
|	лицензии.
|----------------------------------------------------------
|	Build: 			2:56 22.10.2011
|	Last Build: 	4:48 27.10.2011
|	Version file:	1.00 build 2
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$rss 	= new RSS;

class RSS {

	# param
	private		$encoding		= "utf-8";
	private 	$version		= "2.0";
	
	# header link
	public		$rss_link		= "";
	
	protected	$title			= "";
	protected	$description	= "";
	protected	$link			= "";
	protected 	$language		= "ru";
	protected 	$copyright		= "";
	protected 	$managingeditor = "";
	protected 	$webmaster		= "";
	private 	$generator		= "RooCMS";
	protected 	$ttl			= 240;
	public		$lastbuilddate	= 0;
	protected	$image			= array("url"	=>	"",
										"title"	=>	"",
										"link"	=>	"");
	
	# output buffer
	protected	$items			= array();
	public 		$out			= "";
	
	

	//*****************************************************
	// initialisation parametrs
	function init_params() {
		
		global $config, $var;
		
		$this->set_ttl($config->rss_ttl);
		
		$this->managingeditor 	=& $var['sysemail'];
		$this->webmaster 		=& $var['sysemail'];
	}
	
	
	//*****************************************************
	// set params
	public function set_title($title) {
		$this->title = $title;
	}
	
	public function set_description($text) {
		$this->description = $text;
	}

	public function set_link($url) {
		$this->link	= htmlspecialchars($url);
	}
	
	public function set_ttl($ttl) {
		$ttl = round($ttl);
		if($ttl > 60) $this->ttl = $ttl;
	}
	
	public function set_lastbuilddate($date) {
		$now = time();
		if($now > $date) $this->lastbuilddate = $date;
	}
	
	public function set_header_link($script = THIS_SCRIPT) {
	
		global $parse;
		
		$rsslink = $script.".php";
		
		if($parse->uri != "") $rsslink .= $parse->uri."&export=RSS";
		else $rsslink .= "?export=RSS";
		
		$this->rss_link = $parse->transform_uri($rsslink);
	}
	
	
	//*****************************************************
	// draw item
	public function create_item($guid, $title, $description, $link, $pubdate, $author = "", $category = "") {
	
		global $config, $parse;
		
		$link = htmlspecialchars($config->baseurl."/".$parse->transform_uri($link));
		$guid = htmlspecialchars($config->baseurl."/".$parse->transform_uri($guid));
		
		// ??? <![CDATA[ ??? ]]>
		$item  = "\n\t\t <item>";
		$item .= "\n\t\t\t <title>".$title."</title>";
		$item .= "\n\t\t\t <description>".$description."</description>";
		$item .= "\n\t\t\t <link>".$link."</link>";
		$item .= "\n\t\t\t <comments>".$link."</comments>";
		$item .= "\n\t\t\t <pubDate>".gmdate("D, d M Y H:i:s", $pubdate)." GMT</pubDate>";
		$item .= "\n\t\t\t <pubUT>".$pubdate."</pubUT>";
		$item .= "\n\t\t\t <guid isPermaLink='true'>".$guid."</guid>";
		if($category != "") $item .= "\n\t\t\t <category>".$category."</category>";
		if($author != "") 	$item .= "\n\t\t\t <author>".$author."</author>";
		$item .= "\n\t\t </item>";
		
		$this->items[] = $item;
	}
	
	
	//*****************************************************
	// draw header doc
	protected function header() {
		
		global $config, $parse, $var;
		
		
		$this->out .= '<?xml version="1.0" encoding="'.$this->encoding.'"?>'; 
		$this->out .= "\n\n";
		$this->out .= '<rss version="'.$this->version.'" xmlns:roocms="'.$config->baseurl.'/'.THIS_SCRIPT.'.php">';
		$this->out .= "\n\t<channel>";
	
		// title
		if($this->title == "") $this->title =& $var['title'];
		$this->out .= "\n\t\t <title>".$this->title."</title>";
		
		// description
		if($this->description == "") $this->description =& $config->meta_description;
		$this->out .= "\n\t\t <description>".$this->description."</description>";
		
		// link
		if($this->link == "") $this->link = $config->baseurl."/".THIS_SCRIPT.".php".htmlspecialchars($parse->uri);
		$this->out .= "\n\t\t <link>".$this->link."</link>";
	
		// language
		$this->out .= "\n\t\t <language>".$this->language."</language>";
		
		// set email editor
		if($this->managingeditor != "") $this->out .= "\n\t\t <managingEditor>".$this->managingeditor."</managingEditor>";
		
		// set email webmaster
		if($this->webmaster != "") 		$this->out .= "\n\t\t <webMaster>".$this->webmaster."</webMaster>";
		
		// generator
		$this->out .= "\n\t\t <generator>".$this->generator."</generator>";
		
		// ttl
		$this->out .= "\n\t\t <ttl>".$this->ttl."</ttl>";
		
		// image
		$this->image['url'] 	= $config->baseurl."/img/logo.png";
		$this->image['title'] 	= $this->title;
		$this->image['link'] 	= $this->link;
		$this->out .= "\n\t\t <image> \n\t\t\t <url>".$this->image['url']."</url> \n\t\t\t <title>".$this->image['title']."</title> \n\t\t\t <link>".$this->image['link']."</link> \n\t\t </image>";
		
		if($this->lastbuilddate != 0)	$this->out .= "\n\t\t <lastBuildDate>".gmdate("D, d M Y H:i:s", $this->lastbuilddate)." GMT</lastBuildDate>";
	}
	
	
	//*****************************************************
	// draw footer doc
	protected function footer() {
		$this->out .= "\n\t</channel>\n</rss>";
	}
	
	
	//*****************************************************
	// Output RSS
	public function out() {
	
		// set header type
		header("Content-type: text/xml; charset=utf-8");
	
		// init params
		$this->init_params();
		
		// init head document
		$this->header();

		
		// init items
		foreach($this->items AS $key=>$value) {
			$this->out .= $value;
		}
		
		
		// init footer document
		$this->footer();
	
		return $this->out;
	}
	
	
	//*****************************************************
	// check items
	public function check_rss() {
		$c = count($this->items);
		if($c != 0) return true;
		else return false;
	}
}

?>