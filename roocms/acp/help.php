<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2016 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
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
*   along with this program.  If not, see http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
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
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


/**
 * Class ACP_HELP
 */
class ACP_HELP {

	# vars
	private $part		= "help";
	private $part_id	= 0;
	private $part_parent	= 0;

	private $part_data	= array(); # Информация по текущему разделу

	private $helptree 	= array();
	private $breadcumb	= array();



	/**
	* Инициализация раздела помощи
	*
	*/
	public function __construct() {

    		global $roocms, $db, $GET, $POST, $tpl, $smarty;

		# загружаем "дерево" помощи
    		$this->helptree = $this->load_tree();
    		$smarty->assign("tree", $this->helptree);

		# Запрашиваем техническую информацию о разделе по уникальному имени
		if(isset($GET->_u) && $db->check_id($GET->_u, HELP_TABLE, "uname") && !isset($GET->_id)) {
				$q = $db->query("SELECT id, parent_id, uname, title, content, date_modified FROM ".HELP_TABLE." WHERE uname='".$GET->_u."'");
				$row = $db->fetch_assoc($q);

				$this->part = $row['uname'];
				$this->part_id = $row['id'];
				$this->part_parent = $row['parent_id'];

				$this->part_data = $row;
		}

		# Запрашиваем техническую информацию о разделе по идентификатору
		if(isset($GET->_id) && $db->check_id($GET->_id, HELP_TABLE)) {
				$q = $db->query("SELECT id, parent_id, uname, title, content, date_modified FROM ".HELP_TABLE." WHERE id='".$GET->_id."'");
				$row = $db->fetch_assoc($q);

				$this->part = $row['uname'];
				$this->part_id = $row['id'];
				$this->part_parent = $row['parent_id'];

				$this->part_data = $row;
		}

		# Запрашиваем техническую информацию о разделе по умолчанию, если не было верного запроса ни по идентификатору ни уникалному имени
		if((!isset($GET->_id) && !isset($GET->_u)) || $this->part_id == 0) {
				$q = $db->query("SELECT id, parent_id, uname, title, content, date_modified FROM ".HELP_TABLE." WHERE id='1'");
				$row = $db->fetch_assoc($q);

				$this->part = $row['uname'];
				$this->part_id = $row['id'];
				$this->part_parent = $row['parent_id'];

				$this->part_data = $row;
		}


		# Варганим "хлебные хрошки"
		if($this->part_parent != 0) {
			$this->construct_breadcumb($this->part_id);
			krsort($this->breadcumb);
		}

		$smarty->assign('helpmites', $this->breadcumb);

		# действия
		if(DEVMODE) {
			switch($roocms->part) {

				case 'create_part':
					if(isset($POST->create_part)) $this->create_part();
					else $content = $tpl->load_template("help_create_part", true);
					break;

				case 'edit_part':
					if(isset($POST->update_part)) $this->update_part($this->part_id);
					elseif($this->part_id != 0) {
						$q = $db->query("SELECT id, parent_id, uname, title, sort, content FROM ".HELP_TABLE." WHERE id='".$this->part_id."'");
						$data = $db->fetch_assoc($q);

						$smarty->assign("data", $data);
						$content = $tpl->load_template("help_edit_part", true);
					}
					break;

				case 'delete_part':
					$this->delete_part($this->part_id);
					break;

				default:
					$content = $this->show_help();
					break;
			}
		}
		else $content = $this->show_help();

		# отрисовываем шаблон
		$smarty->assign('content', $content);
		$tpl->load_template("help");
	}


	/**
	* Отображаем помошь...
	*
	*/
	private function show_help() {

		global $parse, $tpl, $smarty;

        	$data =& $this->part_data;

		$data['date_modified'] = $parse->date->unix_to_rus($data['date_modified'], false, false, true);
		$data['content'] = $parse->text->html($data['content']);

		# отрисовываем шаблон
		$smarty->assign("subtree", $this->load_tree($data['id']));
		$smarty->assign("data", $data);
		$content = $tpl->load_template('help_view_part', true);


		return $content;
	}


	/**
	* Функция разработчика для добавления разделов помощи
	*
	*/
	private function create_part() {

		global $db, $parse, $POST;

		# предупреждаем возможные ошибки с уникальным именем структурной еденицы
		if(isset($POST->uname) && trim($POST->uname) != "") {
			# избавляем URI от возможных конвульсий
			$POST->uname = strtr($POST->uname, array('-'=>'_','='=>'_'));

			# а так же проверяем что бы алиас не оказался числом
			if(is_numeric($POST->uname)) $POST->uname = randcode(3, "abcdefghijklmnopqrstuvwxyz").$POST->uname;
		}

		# проверяем введенный данные
		if(!isset($POST->title) || trim($POST->title) == "") 					$parse->msg("Не указано название раздела.", false);
		if(!isset($POST->uname) || (trim($POST->uname) == "" && round($POST->uname) != 0)) 	$parse->msg("Не указан uname страницы.", false);
		elseif(!$this->check_uname($POST->uname)) 						$parse->msg("uname раздела не уникален.", false);
		if(!isset($POST->content) || trim($POST->content) == "")				$parse->msg("Отсуствует содержание раздела!", false);

		# если ошибок нет
		if(!isset($_SESSION['error'])) {

			$db->query("INSERT INTO ".HELP_TABLE." (title, uname, sort, content, parent_id, date_modified)
							VALUES ('".$POST->title."', '".$POST->uname."', '".$POST->sort."','".$POST->content."', '".$POST->parent_id."', '".time()."')");

			# пересчитываем "детей"
			$this->count_childs($POST->parent_id);

			# уведомление
			$parse->msg("Раздел успешно добавлен!");

			go(CP."?act=help");
		}
		else go(CP."?act=help&part=create_part");
	}


	/**
	* Функция разработчика для редактирования раздела
	*
	* @param int $id
	*/
	private function update_part($id) {

		global $db, $parse, $POST;

		# Если идентификатор не прошел проверку
		if($id == 0) goback();

		# предупреждаем возможные ошибки с уникальным именем структурной еденицы
		if(isset($POST->uname) && trim($POST->uname) != "") {
			# избавляем URI от возможных конвульсий
			$POST->uname = strtr($POST->uname, array('-'=>'_','='=>'_'));

			# а так же проверяем что бы юнейм не оказался числом
			if(is_numeric($POST->uname)) $POST->uname = randcode(3, "abcdefghijklmnopqrstuvwxyz").$POST->uname;
		}

		# проверяем введенный данные
		if(!isset($POST->title) || trim($POST->title) == "") 					$parse->msg("Не указано название раздела.", false);
		if(!isset($POST->uname) || (trim($POST->uname) == "" && round($POST->uname) != 0)) 	$parse->msg("Не указан uname раздела.", false);
		elseif(!$this->check_uname($POST->uname, $POST->old_uname)) 				$parse->msg("uname раздела не уникален.", false);
		if(!isset($POST->content) || trim($POST->content) == "")				$parse->msg("Отсуствует содержание раздела!", false);

		# если ошибок нет
		if(!isset($_SESSION['error'])) {

			$POST->sort = round($POST->sort);

			# Нельзя менять родителя у главного раздела
			If($id == 1) $POST->parent_id = 0;

			# Если мы назначаем нового родителя
			if($POST->parent_id != $POST->now_parent_id) {

				# Проверим, что не пытаемся быть родителем самим себе
				if($POST->parent_id == $id) {
					$POST->parent_id = $POST->now_parent_id;
					$parse->msg("Не удалось изменить иерархию! Вы не можете изменить иерархию директории назначив её родителем самой себе!", false);
				}
				# ... и что новый родитель это не наш ребенок
				else {
					$childs = $this->load_tree($id);

					if($childs) {
						foreach($childs AS $k=>$v) {
							if($POST->parent_id == $v['id']) {
								$POST->parent_id = $POST->now_parent_id;
								$parse->msg("Не удалось изменить иерархию! Вы не можете изменить иерархию директории переместив её в свой дочерний элемент!", false);
							}
						}
					}
				}
			}

			# Нельзя изменять алиас главной страницы
			if($id == 1 && $POST->uname != "help") {
				$POST->alias = "help";
				$parse->msg("Нельзя изменять uname главной страницы!", false);
			}



			$db->query("UPDATE ".HELP_TABLE." SET title='".$POST->title."', uname='".$POST->uname."', sort='".$POST->sort."', parent_id='".$POST->parent_id."', content='".$POST->content."', date_modified='".time()."' WHERE id='".$id."'");

			# Если мы назначаем нового родителя
			if($POST->parent_id != $POST->now_parent_id) {
				# пересчитываем "детей"
				$this->count_childs($POST->parent_id);
				$this->count_childs($POST->now_parent_id);
			}

			# уведомление
			$parse->msg("Раздел успешно обновлен!");

			go(CP."?act=help&u=".$POST->uname);
		}
		else goback();
	}


	/**
	* Удалить раздел
	*
	* @param int $id
	*/
	private function delete_part($id) {

		global $db, $parse;

		if($id == 0) goback();

		$q = $db->query("SELECT id, parent_id, childs FROM ".HELP_TABLE." WHERE id='".$id."'");
		$row = $db->fetch_assoc($q);

		if($row['childs'] == 0) {

            		$db->query("DELETE FROM ".HELP_TABLE." WHERE id='".$id."'");

			# уведомление
			$parse->msg("Раздел удален");

			# пересчитываем детишек
			$this->count_childs($row['parent_id']);
		}
		else $parse->msg("Невозможно удалить раздел с имеющимися в подчинении подразделами. Сначала перенесите или удалите подразделы.", false);

		goback();
	}


	/**
	 * Собираем дерево "помощи" (шаг 1)
	 *
	 * @param int     $parent   - идентификатор родителя от которого расчитываем "дерево". Указываем его только если хотим не все дерево расчитать, а лишь его часть
	 * @param int     $maxlevel - указываем уровень глубины построения дерева, только если не хотим выводить все дерево.
	 * @param boolean $child    - укажите false если не хотите расчитывать подуровни.
	 *
	 * @return array|bool - вернет флаг false если дерево не собрано, или вернет массив с деревом.
	 */
	public function load_tree($parent=0, $maxlevel=0, $child=true) {

		global $db;
		static $use = false;

		if(!$use) {
			$tree = array();
			$q = $db->query("SELECT id, uname, parent_id, sort, title, childs FROM ".HELP_TABLE." ORDER BY sort ASC, title ASC");
			while($row = $db->fetch_assoc($q)) {
				$row['level']	= 0;
				$tree[] 		= $row;
			}

			$use = true;
		}
		else $tree = $this->helptree;

		# construct tree
		if(isset($tree)) {
			$tree = $this->construct_tree($tree, $parent, $maxlevel, $child);

			# be back
			return $tree;
		}
		else return false;
	}


	/**
	 * Собираем дерево "помощи" (шаг 2)
	 *
	 * @param array   $unit     - массив данных "дерева"
	 * @param int     $parent   - идентификатор родителя от которого расчитываем "дерево". Указываем его только если хотим не все дерево расчитать, а лишь его часть
	 * @param int     $maxlevel - указываем уровень глубины построения дерева, только если не хотим выводить все дерево.
	 * @param boolean $child    - укажите false если не хотите расчитывать подуровни.
	 * @param int     $level    - текущий обрабатываемый уровень (используется прирасчете подкатегорий)
	 *
	 * @return array
	 */
	private function construct_tree(array $unit, $parent=0, $maxlevel=0, $child=true, $level=0) {

		# create array
		if($level == 0) $tree = array();

		foreach($unit AS $i=>$value) {
			if($unit[$i]['parent_id'] == $parent) {
				# update level
				$unit[$i]['level'] = $level;

				# add branch(s)
				$tree[] = $unit[$i];

				# check child
				if($child && ($maxlevel == 0 || $level+1 <= $maxlevel)) {
					$subtree = $this->construct_tree($unit, $unit[$i]['id'], $maxlevel, $child, $level + 1);
					if(is_array($subtree)) $tree = array_merge($tree, $subtree);
				}
			}
		}


		# be back
		if(!empty($tree)) return $tree;
	}


	/**
	* Собираем хлебные крошки по разделу
	*
	* @param int $id - идентификатор текущего раздела
	*/
	private function construct_breadcumb($id = 1) {
		if($id != 1) {
			foreach($this->helptree AS $k=>$v) {
				if($v['id'] == $id) {
					$this->breadcumb[] = array( 'id'	=> $v['id'],
								    'uname'	=> $v['uname'],
								    'title'	=> $v['title']);

					if($v['parent_id'] != 0) $this->construct_breadcumb($v['parent_id']);
				}
			}
		}
	}


	/**
	 * Проверяем на уникальность
	 *
	 * @param string $name    - Проверяемое имя
	 * @param string $without - Исключения для проверяемого уникального имени
	 *
	 * @return bool
	 */
	private function check_uname($name, $without="") {

		global $db;

		$res = false;

		if(trim($without) != trim($name)) {
			$w = "";
			if(trim($without) != "") $w = "uname!='".$without."'";

			if(!$db->check_id($name, HELP_TABLE, "uname", $w)) $res = true;
		}
		else $res = true;

		return $res;
	}


	/**
	* Пересчитываем подразделы указанного раздела
	*
	* @param int $id - Идентификатор раздела, для коготорого произволятся перерасчеты.
	*/
	private function count_childs($id) {

		global $db, $parse;

		$q = $db->query("SELECT count(*) FROM ".HELP_TABLE." WHERE parent_id='".$id."'");
		$c = $db->fetch_row($q);

		$db->query("UPDATE ".HELP_TABLE." SET childs='".$c[0]."' WHERE id='".$id."'");

		# уведомление
		if(DEBUGMODE) $parse->msg("Информация о подразделах для раздела {$id} обновлена.");
	}
}

/**
 * Init Class
 */
$acp_help = new ACP_HELP;

?>