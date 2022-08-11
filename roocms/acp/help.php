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
 * Class ACP_HELP
 */
class ACP_Help {

	# vars
	private $part		= "help";
	private $part_id	= 1;
	private $part_parent	= 0;

	private $part_data	= []; # info active part

	private $helptree 	= [];
	private $breadcrumb	= [];



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

		# Construct breadcrumb
		$this->construct_breadcrumb($this->part_id);
		krsort($this->breadcrumb);

		$smarty->assign('helpmites', $this->breadcrumb);

		# action
		if(DEBUGMODE) {
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

					$q = $db->query("SELECT id, parent_id, uname, title, sort, content FROM ".HELP_TABLE." WHERE id='".$this->part_id."'");
					$data = $db->fetch_assoc($q);

					$smarty->assign("data", $data);
					$content = $tpl->load_template("help_edit_part", true);
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

		return $tpl->load_template('help_view_part', true);
	}


	/**
	* Create new part
	*/
	private function create_part() {

		global $db, $parse, $logger, $post;

		# check & correct uname (alias)
		$post->uname = $parse->text->correct_aliases($post->uname);

		# check data post
		$this->check_post_data("create");

		# if all right
		if(!isset($_SESSION['error'])) {

			$db->query("INSERT INTO ".HELP_TABLE." (title, uname, sort, content, parent_id, date_modified)
							VALUES ('".$post->title."', '".$post->uname."', '".$post->sort."','".$post->content."', '".$post->parent_id."', '".time()."')");
			$id = $db->insert_id();

			# recount childs
			$this->count_childs($post->parent_id);

			# notice
			$logger->info("Раздел помощи #".$id." <".$post->title."> успешно добавлен!");

			# go
			go(CP."?act=help");
		}

		# go
		go(CP."?act=help&part=create_part");
	}


	/**
	* Edit part
	*
	* @param int $id - part id
	*/
	private function update_part(int $id) {

		global $db, $parse, $logger, $post;

		# check & correct uname (alias)
		$post->uname = $parse->text->correct_aliases($post->uname);

		# check data post
		$this->check_post_data("update");

		# if all right
		if(!isset($_SESSION['error'])) {

			$post->sort = round($post->sort);

			# dont change parent_id of main part
			If($id == 1) {
				$post->parent_id = 0;
			}

			# If we set new parent to part
			if($post->parent_id != $post->now_parent_id) {

				# Check that we are not trying to be a parent for ourselves
				if($post->parent_id == $id) {
					$post->parent_id = $post->now_parent_id;
					$logger->error("Не удалось изменить иерархию! Вы не можете изменить иерархию директории назначив её родителем самой себе!");
				}
				# ... and check that new parent is not a child
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

			# Dont change alias for main part
			if($id == 1 && $post->uname != "help") {
				$post->uname = "help";
				$logger->error("Нельзя изменять uname главной страницы!");
			}


			$db->query("UPDATE ".HELP_TABLE." SET 
								title='".$post->title."', 
								uname='".$post->uname."', 
								sort='".$post->sort."', 
								parent_id='".$post->parent_id."', 
								content='".$post->content."', 
								date_modified='".time()."' 
							WHERE 
								id='".$id."'");

			# If we set new parent to part
			if($post->parent_id != $post->now_parent_id) {
				# пересчитываем "детей"
				$this->count_childs($post->parent_id);
				$this->count_childs($post->now_parent_id);
			}

			# logger
			$logger->info("Раздел помощи #".$id." <".$post->title."> успешно обновлен!");

			# go
			go(CP."?act=help&u=".$post->uname);
		}

		# goback
		goback();
	}


	/**
	* Delete part
	*
	* @param int $id - part id
	*/
	private function delete_part(int $id) {

		global $db, $logger;

		if($id == 1) {
			# log
			$logger->error("Невозможно удалить корневой раздел помощи. Это приведет к наработоспосности раздела.");

			# go
			goback();
		}

		$q = $db->query("SELECT id, title, parent_id, childs FROM ".HELP_TABLE." WHERE id='".$id."'");
		$row = $db->fetch_assoc($q);

		if($row['childs'] == 0) {

            		$db->query("DELETE FROM ".HELP_TABLE." WHERE id='".$id."'");

			# logger
			$logger->info("Раздел помощи #".$id." <".$row['title']."> удален");

			# recount childs
			$this->count_childs($row['parent_id']);
		}
		else {
			# logger
			$logger->error("Невозможно удалить раздел с имеющимися в подчинении подразделами. Сначала перенесите или удалите подразделы.");
		}

		goback();
	}


	/**
	 * Construct tree for help part (step 1)
	 *
	 * @param int     $parent   - id for start construction tree.
	 * @param int     $maxlevel - set level for get sublevels, if param == 0, return all sublevels
	 * @param boolean $child    - set false if you dont get sublevels.
	 *
	 * @return array|null|false - return false if tree not construct, or return rray tree.
	 */
	public function load_tree(int $parent=0, int $maxlevel=0, bool $child=true) {

		global $db;
		static $use = false;

		$tree = [];

		if(!$use) {
			$q = $db->query("SELECT id, uname, parent_id, sort, title, childs FROM ".HELP_TABLE." ORDER BY sort, title");
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
	 * Construct tree for help part (step 2)
	 *
	 * @param array   $unit     - fresh data tree
	 * @param int     $parent   - id for start construction tree.
	 * @param int     $maxlevel - set level for get sublevels, if param == 0, return all sublevels
	 * @param boolean $child    - set false if you dont get sublevels.
	 * @param int     $level    - this param for this handler. Dont use handly.
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
	* Construct breadcrumb
	*
	* @param int $id - id start part
	*/
	private function construct_breadcrumb(int $id = 1) {
		if($id != 1) {
			foreach($this->helptree AS $v) {
				if($v['id'] == $id) {
					$this->breadcrumb[] = array( 'id'	=> $v['id'],
								    'uname'	=> $v['uname'],
								    'title'	=> $v['title']);

					if($v['parent_id'] != 0) {
						$this->construct_breadcrumb($v['parent_id']);
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

		# check alias and create cond for data query
		if(isset($get->_u) && $db->check_id($get->_u, HELP_TABLE, "uname")) {
			$cond = "uname='".$get->_u."'";
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
	private function check_uname(string $name, string $without="") {

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
	* Recount subparts
	*
	* @param int $id - id part
	*/
	private function count_childs(int $id) {

		global $db, $logger;

		$c = $db->count(HELP_TABLE, "parent_id='".$id."'");

		$db->query("UPDATE ".HELP_TABLE." SET childs='".$c."' WHERE id='".$id."'");

		# logger
		$logger->info("Информация о подразделах для раздела помощи #".$id." обновлена.");
	}


	/**
	 * Check data post
	 *
	 * @param string $operation - operation type (create|update)
	 */
	private function check_post_data(string $operation="create") {

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
			$logger->error("Не указан uname раздела помощи.");
		}
		elseif(!$this->check_uname($post->uname, $post->old_uname)) {
			$logger->error("uname раздела помощи не уникален.");
		}
	}
}

/**
 * Init Class
 */
$acp_help = new ACP_Help;
