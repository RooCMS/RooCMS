<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class RSS
 * RSS im/ex 2.0
 */
class RSS {

	# param
	private		$encoding		= "utf-8";			# [string]
	private 	$version		= "2.0";			# [string]

	# header link
	public		$rss_link		= "";				# [string]

	protected	$title			= "";				# [string]
	protected	$description		= "";				# [string]
	protected	$link			= "";				# [string]
	protected 	$language		= "ru";				# [string]
	protected 	$copyright		= "";				# [string]
	protected 	$managingeditor 	= "";				# [string]
	protected 	$webmaster		= "";				# [string]
	private 	$generator		= "RooCMS";			# [string]
	protected 	$ttl			= 240;				# [int]
	public		$lastbuilddate		= 0;				# [int]
	protected	$image			= array("url"	=>	"",
						        "title"	=>	"",
							"link"	=>	"");

	# output buffer
	protected	$items			= [];				# [array]
	public 		$out			= "";				# [text]



	/**
	 * initialisation parametrs
	 */
	public function init_params() {

		global $config, $site, $structure;

		$this->set_ttl($config->rss_ttl);
		$this->set_link();
		$this->set_description($structure->page_meta_desc);

		$this->managingeditor 	=& $site['sysemail'];
		$this->webmaster 	=& $site['sysemail'];
	}


	/**
	 * Set title
	 *
	 * @param $title
	 */
	public function set_title($title) {
		$this->title = $title;
	}


	/**
	 * Set description
	 *
	 * @param $text
	 */
	public function set_description($text) {
		$this->description = $text;
	}


	/**
	 * Set link to original
	 *
	 * @param string $url
	 */
	public function set_link(string $url = "") {

		global $site, $parse;

		$uri = str_ireplace("?export=RSS", "", $parse->uri);
		$uri = str_ireplace("&export=RSS", "", $uri);

		if(trim($url) == "") {
			$this->link = $site['domain'] . SCRIPT_NAME . htmlspecialchars($uri);
		}
		else {
			$this->link = htmlspecialchars($url);
		}
	}


	/**
	 * Set time to live
	 *
	 * @param $ttl
	 */
	public function set_ttl($ttl) {

		$ttl = round($ttl);

		if($ttl > 60) {
			$this->ttl = $ttl;
		}
	}


	/**
	 * Set last build date
	 *
	 * @param $date
	 */
	public function set_lastbuilddate($date) {

		$now = time();

		if($now > $date) {
			$this->lastbuilddate = $date;
		}
	}


	/**
	 * Generated link to head
	 */
	public function set_header_link() {

		global $config, $structure, $parse;

		if(!$structure->page_rss || !$config->rss_power) {
			return;
		}

		$rsslink  = SCRIPT_NAME;
		$rsslink .= (trim($parse->uri) != "") ? $parse->uri."&export=RSS" : $rsslink .= "?export=RSS"; ;

		if($config->rss_power) {
			$this->rss_link = $parse->transform_uri($rsslink);
		}
	}


	/**
	 * draw item
	 *
	 * @param        $guid
	 * @param string $title
	 * @param string $description
	 * @param        $link
	 * @param        $pubdate
	 * @param string $author
	 * @param string $category
	 */
	public function create_item($guid, string $title, string $description, $link, $pubdate, string $author = "", string $category = "") {

		global $site, $parse;

		$link = htmlspecialchars($site['domain'].$parse->transform_uri($link));
		$guid = htmlspecialchars($site['domain'].$parse->transform_uri($guid));

		# ??? <![CDATA[ ??? ]]>
		$item  = "\n\t\t <item>";
		$item .= "\n\t\t\t <title>".$title."</title>";
		$item .= "\n\t\t\t <description>".$description."</description>";
		$item .= "\n\t\t\t <link>".$link."</link>";
		$item .= "\n\t\t\t <comments>".$link."</comments>";
		$item .= "\n\t\t\t <pubDate>".gmdate("D, d M Y H:i:s", $pubdate)." GMT</pubDate>";
		$item .= "\n\t\t\t <pubUT>".$pubdate."</pubUT>";
		$item .= "\n\t\t\t <guid isPermaLink='true'>".$guid."</guid>";

		if(trim($category) != "") {
			$item .= "\n\t\t\t <category>".$category."</category>";
		}

		if(trim($author) != "") {
			$item .= "\n\t\t\t <author>".$author."</author>";
		}
		$item .= "\n\t\t </item>";

		$this->items[] = $item;
	}


	/**
	 * Draw header doc
	 */
	protected function header() {

		global $config, $site;


		$this->out .= '<?xml version="1.0" encoding="'.$this->encoding.'"?>';
		$this->out .= "\n\n";
		$this->out .= '<rss version="'.$this->version.'" xmlns:roocms="'.$site['domain'].SCRIPT_NAME.'">';
		$this->out .= "\n\t<channel>";

		# title
		if(trim($this->title) == "") {
			$this->title =& $site['title'];
		}
		$this->out .= "\n\t\t <title>".$this->title."</title>";

		# description
		if(trim($this->description) == "") {
			$this->description =& $config->meta_description;
		}
		$this->out .= "\n\t\t <description>".$this->description."</description>";

		# link
		$this->out .= "\n\t\t <link>".$this->link."</link>";

		# language
		$this->out .= "\n\t\t <language>".$this->language."</language>";

		# set email editor
		if(trim($this->managingeditor) != "") {
			$this->out .= "\n\t\t <managingEditor>".$this->managingeditor."</managingEditor>";
		}

		# set email webmaster
		if(trim($this->webmaster) != "") {
			$this->out .= "\n\t\t <webMaster>".$this->webmaster."</webMaster>";
		}

		# generator
		$this->out .= "\n\t\t <generator>".$this->generator."</generator>";

		# ttl
		$this->out .= "\n\t\t <ttl>".$this->ttl."</ttl>";

		# image
		$this->image['url'] 	= $site['domain']."/img/logo.png";
		$this->image['title'] 	= $this->title;
		$this->image['link'] 	= $this->link;
		$this->out .= "\n\t\t <image> \n\t\t\t <url>".$this->image['url']."</url> \n\t\t\t <title>".$this->image['title']."</title> \n\t\t\t <link>".$this->image['link']."</link> \n\t\t </image>";

		if($this->lastbuilddate != 0) {
			$this->out .= "\n\t\t <lastBuildDate>".gmdate("D, d M Y H:i:s", $this->lastbuilddate)." GMT</lastBuildDate>";
		}
	}


	/**
	 * draw footer doc
	 */
	protected function footer() {
		$this->out .= "\n\t</channel>\n</rss>";
	}


	/**
	 * Output result
	 *
	 * @return string
	 */
	public function out() {

		# set header type
		header("Content-type: text/xml; charset=utf-8");

		# init params
		$this->init_params();

		# init head document
		$this->header();


		# init items
		foreach($this->items AS $value) {
			$this->out .= $value;
		}


		# init footer document
		$this->footer();

		return $this->out;
	}


	/**
	 * Check items
	 *
	 * @return bool
	 */
	public function check_rss() {

		$c = count($this->items);

		return $c != 0;
	}
}
