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
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class ACP_PAGES_PHP
 */
class ACP_Pages_PHP {

	/**
	* Edit content
	*
	* @param int $sid - Structure id
	*/
	public function edit(int $sid) {

		global $db, $tpl, $smarty, $parse;

		$q = $db->query("SELECT h.id, h.sid, h.content, p.title, p.alias, p.meta_description, p.meta_keywords, h.date_modified
							FROM ".PAGES_PHP_TABLE." AS h
							LEFT JOIN ".STRUCTURE_TABLE." AS p ON (p.id = h.sid)
							WHERE h.sid='".$sid."'");
		$data = $db->fetch_assoc($q);
		$data['lm'] = $parse->date->unix_to_rus($data['date_modified'], true, true, true);

		$smarty->assign("data", $data);

		$content = $tpl->load_template("pages_edit_php", true);

		$smarty->assign("content", $content);
	}


	/**
	* Update page content
	*
	* @param mixed $data - this object data params
	*/
	public function update($data) {

		global $db, $logger, $post;

		$db->query("UPDATE ".PAGES_PHP_TABLE." SET content='".$post->content."', date_modified='".time()."' WHERE sid='".$data->page_sid."'");

		$logger->info("Страница #".$data->page_sid." успешно обновлена.");

		goback();
	}


	/**
	* Remove page
	*
	* @param int $sid - Structure id
	*/
	public function delete(int $sid) {

		global $db;

		# del pageunit
		$db->query("DELETE FROM ".PAGES_PHP_TABLE." WHERE sid='".$sid."'");
	}
}
