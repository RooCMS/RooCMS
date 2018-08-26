<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * Contacts: <info@roocms.com>
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.4
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
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
	* Редактирование PHP страницы
	*
	* @param boolean $sid - Структурный идентификатор
	*/
	public function edit($sid) {

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
	* Функция обновления PHP страницы
	*
	* @param boolean $sid - структурный идентификатор
	*/
	public function update($sid) {

		global $db, $logger, $post;

		$db->query("UPDATE ".PAGES_PHP_TABLE." SET content='".$post->content."', date_modified='".time()."' WHERE sid='".$sid."'");

		$logger->info("Страница #".$sid." успешно обновлена.");

		goback();
	}


	/**
	* Фнкция удаления PHP страницы
	*
	* @param boolean $sid - структурная еденица
	*/
	public function delete($sid) {

		global $db, $logger;

		# del pageunit
		$db->query("DELETE FROM ".PAGES_PHP_TABLE." WHERE sid='".$sid."'");

		# notice
		$logger->info("Страница #".$sid." успешно удалена");
	}
}