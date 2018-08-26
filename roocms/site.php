<?php
/**
 *   RooCMS - Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   You should have received a copy of the GNU General Public License v3
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
* @package      RooCMS
* @subpackage   Frontend
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
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
$site['title']		= $structure->page_title;
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
	require_once "site_module.php";
}


if($structure->access) {
	if(trim($roocms->part) == "") {
		/**
		* Load structure unit
		*/
		switch($structure->page_type) {
			case 'html':
				require_once "site_page_html.php";
				$page_html = new SitePageHTML;
				break;

			case 'php':
				require_once "site_page_php.php";
				$page_php = new SitePagePHP;
				break;

			case 'feed':
				require_once "site_page_feed.php";
				$page_feed = new SitePageFeed;
				break;
		}
	}
}
else {
	$tpl->load_template("access_denied");
}