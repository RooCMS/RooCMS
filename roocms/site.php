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
* Meta SEO
*
*/
$site['title']		= $structure->page_meta_title;
$site['description']	= $structure->page_meta_desc;
if(!empty($site)) {
	$site['keywords']	= $structure->page_meta_keys;
}



/**
 * Init Blocks & Modules
 */
if(!class_exists("Blocks"))  {
	require_once "site_blocks.php";
}
if(!class_exists("Modules")) {
	require_once "site_modules.php";
}


if($structure->access) {
	if(trim($roocms->part) == "") {
		/**
		* Load structure unit
		*/
		switch($structure->page_type) {
			case 'html':
				require_once "site_pageHTML.php";
				$page_html = new PageHTML;
				break;

			case 'php':
				require_once "site_pagePHP.php";
				$page_php = new PagePHP;
				break;

			case 'feed':
				require_once _CLASS."/trait_feedExtends.php";
				require_once "site_pageFeed.php";
				$page_feed = new PageFeed;
				break;
		}
	}
}
else {
	$tpl->load_template("access_denied");
}
