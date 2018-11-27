<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2019 alexandr Belov aka alex Roosso. All rights reserved.
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
 * Class ACP_HELP
 */
class ACP_Help {

	# vars
	private $part		= "help";
	private $part_id	= 0;
	private $part_parent	= 0;

	private $part_data	= []; # info active part

	private $helptree 	= [];
	private $breadcumb	= [];



	/**
	* Construct
	*
	*/
	public function __construct() {

    		global $roocms, $db, $post, $tpl, $smarty;

		# Load structure tree
    		$this->helptree = $this->load_tree();
		$smarty->assign("tree", $this->helptree);

		# Load data
		$this->load_data();

		# Construct breadcumb
		$this->construct_breadcumb($this->part_id);
		krsort($this->breadcumb);

		$smarty->assign('helpmites', $this->breadcumb);

		# action
		if(DEVMODE) {
			switch($roocms->part) {

				case 'create_part':
					if(isset($post->create_part)) {
						$this->create_part();
					}
					else {
						$content = $tpl->load_template("help_create_part", true);
					}
					break;

				case 'edit_part':
					if(isset($post->update_part)) {
						$this->update_part($this->part_id);
					}
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
		else {
			$content = $this->show_help();
		}

		# tpl
		$smarty->assign('content', $content);
		$tpl->load_template("help");
	}


	/**
	 * Show help part
	 *
	 * @return string|null tpl
	 */
	private function show_help() {

		global $parse, $tpl, $smarty;

        	$data =& $this->part_data;

		$data['date_modified'] = $parse->date->unix_to_rus($data['date_modified'], false, false, true);
		$data['content'] = $parse->text->html($data['content']);

		# tpl
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

		global $db, $parse, $logger, $post;

		# предупреждаем возможные ошибки с уникальным именем структурной еденицы
		if(isset($post->uname)) {
			$post->uname = $parse->text->correct_aliases($post->uname);
		}

		# проверяем введенный данные
		$this->check_post_data("create");


		# если ошибок нет
		if(!isset($_SESSION['error'])) {

			$db->query("INSERT INTO ".HELP_TABLE." (title, uname, sort, content, parent_id, date_modified)
							VALUES ('".$post->title."', '".$post->uname."', '".$post->sort."','".$post->content."', '".$post->parent_id."', '".time()."')");
			$id = $db->insert_id();

			# пересчитываем "детей"
			$this->count_childs($post->parent_id);

			# уведомление
			$logger->info("Раздел #".$id." успешно добавлен!");

			# go
			go(CP."?act=help");
		}

		go(CP."?act=help&part=create_part");
	}


	/**
	* Функция разработчика для редактирования раздела
	*
	* @param int $id
	*/
	private function update_part($id) {

		global $db, $parse, $logger, $post;

		# Если идентификатор не прошел проверку
		if($id == 0) {
			goback();
		}

		# предупреждаем возможные ошибки с уникальным именем структурной еденицы
		if(isset($post->uname)) {
			$post->uname = $parse->text->correct_aliases($post->uname);
		}

		# проверяем введенный данные
		$this->check_post_data("update");


		# если ошибок нет
		if(!isset($_SESSION['error'])) {

			$post->sort = round($post->sort);

			# Нельзя менять родителя у главного раздела
			If($id == 1) {
				$post->parent_id = 0;
			}

			# Если мы назначаем нового родителя
			if($post->parent_id != $post->now_parent_id) {

				# Проверим, что не пытаемся быть родителем самим себе
				if($post->parent_id == $id) {
					$post->parent_id = $post->now_parent_id;
					$logger->error("Не удалось изменить иерархию! Вы не можете изменить иерархию директории назначив её родителем самой себе!");
				}
				# ... и что новый родитель это не наш ребенок
				else {
					$childs = $this->load_tree($id);

					foreach((array)$childs AS $v) {
						if($post->parent_id == $v['id']) {
							$post->parent_id = $post->now_parent_id;
							$logger->error("Не удалось изменить иерархию! Вы не можете изменить иерархию директории переместив её в свой дочерний элемент!");
						}
					}
				}
			}

			# Нельзя изменять алиас главной страницы
			if($id == 1 && $post->uname != "help") {
				$post->uname = "help";
				$logger->error("Нельзя изменять uname главной страницы!");
			}


			$db->query("UPDATE ".HELP_TABLE." SET title='".$post->title."', uname='".$post->uname."', sort='".$post->sort."', parent_id='".$post->parent_id."', content='".$post->content."', date_modified='".time()."' WHERE id='".$id."'");

			# Если мы назначаем нового родителя
			if($post->parent_id != $post->now_parent_id) {
				# пересчитываем "детей"
				$this->count_childs($post->parent_id);
				$this->count_childs($post->now_parent_id);
			}

			# logger
			$logger->info("Раздел #".$id." успешно обновлен!");

			# go
			go(CP."?act=help&u=".$post->uname);
		}

		# goback
		goback();
	}


	/**
	* Удалить раздел
	*
	* @param int $id
	*/
	private function delete_part($id) {

		global $db, $logger;

		if($id == 0) {
			goback();
		}

		$q = $db->query("SELECT id, parent_id, childs FROM ".HELP_TABLE." WHERE id='".$id."'");
		$row = $db->fetch_assoc($q);

		if($row['childs'] == 0) {

            		$db->query("DELETE FROM ".HELP_TABLE." WHERE id='".$id."'");

			# logger
			$logger->info("Раздел #".$id." удален");

			# пересчитываем детишек
			$this->count_childs($row['parent_id']);
		}
		else {
			$logger->error("Невозможно удалить раздел с имеющимися в подчинении подразделами. Сначала перенесите или удалите подразделы.");
		}

		goback();
	}


	/**
	 * Собираем дерево "помощи" (шаг 1)
	 *
	 * @param int     $parent   - идентификатор родителя от которого расчитываем "дерево". Указываем его только если хотим не все дерево расчитать, а лишь его часть
	 * @param int     $maxlevel - указываем уровень глубины построения дерева, только если не хотим выводить все дерево.
	 * @param boolean $child    - укажите false если не хотите расчитывать подуровни.
	 *
	 * @return array|null|false - вернет флаг false если дерево не собрано, или вернет массив с деревом.
	 */
	public function load_tree($parent=0, $maxlevel=0, $child=true) {

		global $db;
		static $use = false;

		$tree = [];

		if(!$use) {
			$q = $db->query("SELECT id, uname, parent_id, sort, title, childs FROM ".HELP_TABLE." ORDER BY sort ASC, title ASC");
			while($row = $db->fetch_assoc($q)) {
				$row['level']	= 0;
				$tree[] 	= $row;
			}

			$use = true;
		}
		else {
			$tree = $this->helptree;
		}

		# construct tree
		if(!empty($tree)) {
			$tree = $this->construct_tree($tree, $parent, $maxlevel, $child);

			# be back
			return $tree;
		}
		else {
			return false;
		}
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
	 * @return array|null
	 */
	private function construct_tree(array $unit, $parent=0, $maxlevel=0, $child=true, $level=0) {

		# create array
		if($level == 0) {
			$tree = array();
		}

		foreach($unit AS $i=>$value) {
			if($unit[$i]['parent_id'] == $parent) {
				# update level
				$unit[$i]['level'] = $level;

				# add branch(s)
				$tree[] = $unit[$i];

				# check child
				if($child && ($maxlevel == 0 || $level+1 <= $maxlevel)) {
					$subtree = $this->construct_tree($unit, $unit[$i]['id'], $maxlevel, $child, $level + 1);
					if(is_array($subtree)) {
						$tree = array_merge($tree, $subtree);
					}
				}
			}
		}


		# be back
		if(!empty($tree)) {
			return $tree;
		}
	}


	/**
	* Собираем хлебные крошки по разделу
	*
	* @param int $id - идентификатор текущего раздела
	*/
	private function construct_breadcumb($id = 1) {
		if($id != 1) {
			foreach($this->helptree AS $v) {
				if($v['id'] == $id) {
					$this->breadcumb[] = array( 'id'	=> $v['id'],
								    'uname'	=> $v['uname'],
								    'title'	=> $v['title']);

					if($v['parent_id'] != 0) {
						$this->construct_breadcumb($v['parent_id']);
					}
				}
			}
		}
	}


	/**
	 * Loading data part
	 */
	private function load_data() {

		global $db, $get;

		# default: load root
		$cond = "id='1'";

		# Запрашиваем техническую информацию о разделе по уникальному имени
		if(isset($get->_u) && $db->check_id($get->_u, HELP_TABLE, "uname")) {
			$cond = "uname='".$get->_u."'";
		}

		# Запрашиваем техническую информацию о разделе по идентификатору
		if(isset($get->_id) && $db->check_id($get->_id, HELP_TABLE)) {
			$cond = "id='".$get->_id."'";
		}

		# query data
		$q = $db->query("SELECT id, parent_id, uname, title, content, date_modified FROM ".HELP_TABLE." WHERE ".$cond);
		$row = $db->fetch_assoc($q);

		$this->part = $row['uname'];
		$this->part_id = $row['id'];
		$this->part_parent = $row['parent_id'];

		$this->part_data = $row;
	}


	/**
	 * Check alias name on unique
	 *
	 * @param string $name    - uname
	 * @param string $without - exclude uname
	 *
	 * @return bool
	 */
	private function check_uname($name, $without="") {

		global $db;

		$res = false;

		if(trim($without) != trim($name)) {

			$without = trim($without);
			$w = "uname!='".$without."'";

			if(!$db->check_id($name, HELP_TABLE, "uname", $w)) {
				$res = true;
			}
		}
		else {
			$res = true;
		}

		return $res;
	}


	/**
	* Пересчитываем подразделы указанного раздела
	*
	* @param int $id - Идентификатор раздела, для коготорого произволятся перерасчеты.
	*/
	private function count_childs($id) {

		global $db, $logger;

		$c = $db->count(HELP_TABLE, "parent_id='".$id."'");

		$db->query("UPDATE ".HELP_TABLE." SET childs='".$c."' WHERE id='".$id."'");

		# logger
		$logger->info("Информация о подразделах для раздела #".$id." обновлена.");
	}


	/**
	 * Функция проверяет поступающие в массиве $post данные при создании или обновлении раздела
	 *
	 * @param string $operation - Тип операции (create|update)
	 */
	private function check_post_data($operation="create") {

		global $post, $logger;

		# operation type
		if($operation == "create") {
			$post->old_uname = "";
		}

		# checked
		if(!isset($post->title)) {
			$logger->error("Не указано название раздела.");
		}
		if(!isset($post->uname) && round($post->uname) != 0) {
			$logger->error("Не указан uname раздела.");
		}
		elseif(!$this->check_uname($post->uname, $post->old_uname)) {
			$logger->error("uname раздела не уникален.");
		}

		if(!isset($post->content)) {
			$post->content = "";
		}
	}
}

/**
 * Init Class
 */
$acp_help = new ACP_Help;