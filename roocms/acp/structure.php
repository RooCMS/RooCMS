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
 * Class ACP_STRUCTURE
 */
class ACP_Structure {

	# vars
	private	$engine;
	private $unit;

	private $sid = 0;



	/**
	 * Lets mortal kombat begin
	 */
	public function __construct() {

		require_once _CLASS."/class_structure.php";
		$this->engine = new Structure(false);

		# initialise
		$this->init();
	}


	/**
	 * initialisation action
	 */
	private function init() {

		global $roocms, $config, $db, $users, $tpl, $smarty, $get, $post;

		# read site tree
		$smarty->assign('tree', $this->engine->sitetree);

		# Check enabled page type
		$content_types = [];
		foreach($this->engine->content_types AS $key=>$value) {
			$content_types[$key] = $value['title'];
		}
		$smarty->assign('content_types', $content_types);


		# default thumb size
		$default_thumb_size = array('width'  => $config->gd_thumb_image_width,
					    'height' => $config->gd_thumb_image_height);
		$smarty->assign("default_thumb_size", $default_thumb_size);


		# Check id
		if(isset($get->_id) && $db->check_id($get->_id, STRUCTURE_TABLE)) {
			$this->sid = $get->_id;
		}


		# action
		switch($roocms->part) {
			# create
			case 'create':
				if(isset($post->create_unit)) {
					$this->create_unit();
				}
				else {
					# list groups
					$groups = $users->get_usergroups();

					# tpl
					$smarty->assign("groups", $groups);
					$content = $tpl->load_template("structure_create", true);
				}
				break;

			# edit and update
			case 'edit':
				if(isset($post->update_unit)) {
					$this->update_unit($this->sid);
				}

				$content = $this->edit_unit($this->sid);
				break;

			# delete
			case 'delete':
				$this->delete_unit($this->sid);
				break;

			default:
				$content = $tpl->load_template("structure_tree", true);
				break;
		}

		# tpl
		$smarty->assign('content', $content);
		$tpl->load_template("structure");
	}


	/**
	 * Create new structure unit
	 */
	private function create_unit() {

		global $db, $logger, $post;

		# check unit parametrs
		$this->check_unit_parametrs();


		if(!isset($_SESSION['error'])) {

			# check parent type
			$q = $db->query("SELECT page_type FROM ".STRUCTURE_TABLE." WHERE id='".$post->parent_id."'");
			$d = $db->fetch_assoc($q);

			# not add another page type to feed
			if($d['page_type'] == "feed" && $post->page_type != "feed") {
				$logger->error("Вы не можете установить для ленты в качестве дочерней страницы другой структурный элемент, кроме ленты.");
				goback();
			}

			# insert structure int data
			$db->query("INSERT INTO ".STRUCTURE_TABLE."    (alias, title, parent_id, nav, group_access, page_type, meta_title, meta_description, meta_keywords, noindex, sort, date_create, date_modified, thumb_img_width, thumb_img_height)
								VALUES ('".$post->alias."', '".$post->title."', '".$post->parent_id."', '".$post->nav."', '".$post->gids."', '".$post->page_type."', '".$post->meta_title."', '".$post->meta_description."', '".$post->meta_keywords."', '".$post->noindex."', '".$post->sort."', '".time()."', '".time()."', '".$post->thumb_img_width."', '".$post->thumb_img_height."')");
			$sid = $db->insert_id();

			# create body unit for html & php pages
			switch($post->page_type) {
				case 'html':
					$db->query("INSERT INTO ".PAGES_HTML_TABLE." (sid, date_modified) VALUE ('".$sid."', '".time()."')");
					break;

				case 'story':
					$db->query("INSERT INTO ".PAGES_STORY_TABLE." (sid, date_modified) VALUE ('".$sid."', '".time()."')");
					break;

				case 'php':
					$db->query("INSERT INTO ".PAGES_PHP_TABLE." (sid, date_modified) VALUE ('".$sid."', '".time()."')");
					break;

				case 'feed':
					$db->query("UPDATE ".STRUCTURE_TABLE." SET rss='1' WHERE sid='".$sid."'");
					break;
			}

			# recount childs
			$this->count_childs($post->parent_id);

			# notice
			$logger->info("Элемент структуры #".$sid." успешно добавлена.");

			# go
			if(isset($post->create_unit['ae'])) {
				go(CP."?act=structure");
			}

			if($post->page_type == "feed") {
				go(CP."?act=feeds&part=control&page=".$sid);
			}

			go(CP."?act=pages&part=edit&page=".$sid);
		}

		# goback
		goback();
	}


	/**
	 * Edit structure unit
	 *
	 * @param mixed $sid - Structure id
	 *
	 * @return string
	 */
	private function edit_unit($sid) {

		global $db, $users, $smarty, $tpl;

		$q = $db->query("SELECT id, parent_id, nav, group_access, alias, title, meta_title, meta_description, meta_keywords, noindex, sort, page_type, thumb_img_width, thumb_img_height FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
		$data = $db->fetch_assoc($q);

		# check access granted for groups
		$gids = $users->get_gid_access_granted($data['group_access']);

		# list groups
		$groups = $users->get_usergroups();

		# tpl
		$smarty->assign("gids",   $gids);
		$smarty->assign("groups", $groups);
		$smarty->assign("data",   $data);

		return $tpl->load_template("structure_edit", true);
	}


	/**
	 * Update structure unit
	 *
	 * @param mixed $sid - structure id
	 */
	private function update_unit($sid) {

		global $db, $logger, $post;

		# dont change parent, alias and nav flag for main page
		If($sid == 1) {
			$post->parent_id = 0;
			$post->alias     = "index";
			$post->nav       = 1;
		}

		# check unit parametrs
		$this->check_unit_parametrs();


		if(!isset($_SESSION['error'])) {

			# if set new parent
			if($post->parent_id != $post->now_parent_id) {

				# Check that we are not trying to be a parent for ourselves
				if($post->parent_id == $sid) {
					$post->parent_id = $post->now_parent_id;
					$logger->error("Не удалось изменить иерархию! Вы не можете назначить страницу подчиненной самой себе!");
				}
				# ... and check that new parent is not a child
				else {
					$childs = $this->engine->load_tree($sid);

					foreach((array)$childs AS $v) {
						if($post->parent_id == $v['id']) {
							$post->parent_id = $post->now_parent_id;
							$logger->error("Не удалось изменить иерархию! Вы не можете поместить страницу в подчинение странице собственного нижнего уровня!");
						}
					}
				}
			}

			# get parent type
			$q = $db->query("SELECT page_type FROM ".STRUCTURE_TABLE." WHERE id='".$post->parent_id."'");
			$d = $db->fetch_assoc($q);

			# get page type
			$q = $db->query("SELECT page_type FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
			$n = $db->fetch_assoc($q);

			# not add another page type to feed
			if($d['page_type'] == "feed" && $n['page_type'] != "feed") {
				$logger->error("Вы не можете установить для ленты в качестве дочерней страницы другой структурный элемент, кроме ленты.");
				$post->parent_id = $post->now_parent_id;
			}

			# DB
			$db->query("UPDATE ".STRUCTURE_TABLE."
					SET
						alias='".$post->alias."',
						title='".$post->title."',
						parent_id='".$post->parent_id."',
						nav='".$post->nav."',
						group_access='".$post->gids."',
						meta_title='".$post->meta_title."',
						meta_description='".$post->meta_description."',
						meta_keywords='".$post->meta_keywords."',
						noindex='".$post->noindex."',
						sort='".$post->sort."',
						date_modified='".time()."',
						thumb_img_width='".$post->thumb_img_width."',
						thumb_img_height='".$post->thumb_img_height."'
					WHERE
						id='".$sid."'");

			# if set new parent
			if($post->parent_id != $post->now_parent_id) {
				# recount childs
				$this->count_childs($post->parent_id);
				$this->count_childs($post->now_parent_id);
			}

			# notice
			$logger->info("Страница #".$sid." успешно обновлена.");

			# go
			if(isset($post->update_unit['ae'])) {
				go(CP."?act=structure");
			}

			go(CP."?act=structure&part=edit&id=".$sid);
		}

		# goback
		goback();
	}


	/**
	 * Remove structure unit
	 *
	 * @param mixed $sid - structure id
	 */
	private function delete_unit($sid) {

		global $db, $logger;

		$q = $db->query("SELECT childs, parent_id, page_type FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
		$c = $db->fetch_assoc($q);

		if($c['childs'] == 0) {

			switch($c['page_type']) {

				case 'html': # del content html
					require_once _ROOCMS."/acp/pages_html.php";
					$this->unit = new ACP_Pages_HTML;
					break;

				case 'story': # del content story
					require_once _ROOCMS."/acp/pages_story.php";
					$this->unit = new ACP_Pages_Story;
					break;

				case 'php': # del content php
					require_once _ROOCMS."/acp/pages_php.php";
					$this->unit = new ACP_Pages_PHP;
					break;

				case 'feed': # del content feed
					require_once _ROOCMS."/acp/feeds_feed.php";
					$this->unit = new ACP_Feeds_Feed();
					break;
			}

			# remove
			$this->unit->delete($sid);

			# structure unit
			$db->query("DELETE FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");

			# notice
			$logger->info("Страница #".$sid." успешно удалена");

			# recount parent childs
			$this->count_childs($c['parent_id']);
		}
		else {
			$logger->error("Невозможно удалить страницу, по причине имеющихся у страницы дочерних связей. Сначала перенесите или удалите дочерние страницы.");
		}

		# go
		goback();
	}


	/**
	 * Check alias name on unique
	 *
	 * @param string $name    - uname
	 * @param string $without - exclude uname
	 *
	 * @return bool
	 */
	private function check_unique_alias(string $name, string $without="") {

		global $db;

		$res = false;

		if(trim($without) != trim($name)) {

			$without = trim($without);
			$w = "alias!='".$without."'";

			if(!$db->check_id($name, STRUCTURE_TABLE, "alias", $w)) {
				$res = true;
			}
		}
		else {
			$res = true;
		}

		return $res;
	}


	/**
	 * Check nav bool for use
	 */
	private function check_nav() {

		global $post, $logger;

		if(isset($this->engine->sitetree[$post->parent_id]['nav']) && $this->engine->sitetree[$post->parent_id]['nav'] == 0) {
			$post->nav = 0;
			$logger->info("Прежде чем включить в навигацию эту страницу сайта, вы должны включить в навигацию родительскую", false);
		}
	}


	/**
	 * Alias handler
	 */
	private function handler_alias() {

		global $parse, $post;

		if(trim($post->alias) == "") {
			$post->alias = $post->title;
		}

		# clear alias from trash symbols
		$post->alias = str_ireplace(array(' ','-','='), '_', $post->alias);

		# correct alias
		$post->alias = $parse->text->correct_aliases($post->alias);
	}


	/**
	 * Check unit data
	 */
	private function check_unit_parametrs() {

		global $logger, $post, $img;

		# title
		if(!isset($post->title)) {
			$logger->error("Не указано название страницы.");
		}

		# alias
		$this->handler_alias();
		if(!isset($post->old_alias)) {
			$post->old_alias = "";
		}
		if(!$this->check_unique_alias($post->alias, $post->old_alias)) {
			$logger->error("Алиас страницы не уникален.");
		}

		# group access
		if(isset($post->gids) && is_array($post->gids)) {
			$post->gids = implode(",", $post->gids);
		}
		else {
			$post->gids = 0;
		}

		# sort
		$post->sort = round($post->sort);

		# nav
		$this->check_nav();

		# thumbnail check
		$img->check_post_thumb_parametrs();
	}


	/**
	 * Recount subparts
	 *
	 * @param int $sid - structure id
	 */
	private function count_childs(int $sid) {

		global $db;

		$c = $db->count(STRUCTURE_TABLE, "parent_id='".$sid."'");

		$db->query("UPDATE ".STRUCTURE_TABLE." SET childs='".$c."' WHERE id='".$sid."'");
	}
}

/**
 * Init Class
 */
$acp_structure = new ACP_Structure;
