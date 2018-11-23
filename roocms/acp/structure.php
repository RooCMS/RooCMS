<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
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

		global $roocms, $config, $db, $tpl, $smarty, $get, $post;

		# считываем "дерево"
		$smarty->assign('tree', $this->engine->sitetree);

		# Проверяем разрешенные типы страниц для использования
		$content_types = [];
		foreach($this->engine->content_types AS $key=>$value) {
			if($value['enable']) {
				$content_types[$key] = $value['title'];
			}
		}
		$smarty->assign('content_types', $content_types);


		# default thumb size
		$default_thumb_size = array('width'	=> $config->gd_thumb_image_width,
					    'height'	=> $config->gd_thumb_image_height);
		$smarty->assign("default_thumb_size", $default_thumb_size);


		# Проверяем идентификатор
		if(isset($get->_id) && $db->check_id($get->_id, STRUCTURE_TABLE)) {
			$this->sid = $get->_id;
		}


		# действуем
		switch($roocms->part) {
			# create
			case 'create':
				if(isset($post->create_unit)) {
					$this->create_unit();
				}
				else {
					# groups
					$groups = [];
					$q = $db->query("SELECT gid, title, users FROM ".USERS_GROUP_TABLE." ORDER BY gid ASC");
					while($row = $db->fetch_assoc($q)) {
						$groups[] = $row;
					}

					# шаблонизируем... (слово то какое...)
					$smarty->assign("groups", $groups);
					$content = $tpl->load_template("structure_create", true);
				}
				break;

			# edit and update
			case 'edit':
				if(isset($post->update_unit)) {
					$this->update_unit($this->sid);
				}
				elseif($this->sid != 0) {
					$content = $this->edit_unit($this->sid);
				}
				else {
					go(CP);
				}
				break;

			# delete
			case 'delete':
				$this->delete_unit($this->sid);
				break;

			default:
				$content = $tpl->load_template("structure_tree", true);
				break;
		}

		# отрисовываем шаблон
		$smarty->assign('content', $content);
		$tpl->load_template("structure");
	}


	/**
	 * Создаем новый структурный элемент
	 */
	private function create_unit() {

		global $db, $logger, $post;

		# check unit parametrs
		$this->check_unit_parametrs();


		if(!isset($_SESSION['error'])) {
			$post->sort = round($post->sort);

			# проверяем тип родителя
			$q = $db->query("SELECT page_type FROM ".STRUCTURE_TABLE." WHERE id='".$post->parent_id."'");
			$d = $db->fetch_assoc($q);

			# Нельзя к лентам добавлять другие дочерние элементы, кроме таких же лент.
			if($d['page_type'] == "feed" && $post->page_type != "feed") {
				$logger->error("Вы не можете установить для ленты в качестве дочерней страницы другой структурный элемент, кроме ленты.");
				goback();
			}

			# добавляем структурную еденицу
			$db->query("INSERT INTO ".STRUCTURE_TABLE."    (alias, title, parent_id, group_access, page_type, meta_description, meta_keywords, noindex, sort, date_create, date_modified, thumb_img_width, thumb_img_height)
								VALUES ('".$post->alias."', '".$post->title."', '".$post->parent_id."', '".$post->gids."', '".$post->page_type."', '".$post->meta_description."', '".$post->meta_keywords."', '".$post->noindex."', '".$post->sort."', '".time()."', '".time()."', '".$post->thumb_img_width."', '".$post->thumb_img_height."')");
			$sid = $db->insert_id();

			# create body unit for html & php pages
			switch($post->page_type) {
				case 'html':
					$db->query("INSERT INTO ".PAGES_HTML_TABLE." (sid, date_modified) VALUE ('".$sid."', '".time()."')");
					# get body unit id
					$page_id = $db->insert_id();
					$db->query("UPDATE ".STRUCTURE_TABLE." SET page_id='".$page_id."', rss='0' WHERE id='".$sid."'");
					break;

				case 'php':
					$db->query("INSERT INTO ".PAGES_PHP_TABLE." (sid, date_modified) VALUE ('".$sid."', '".time()."')");
					# get body unit id
					$page_id = $db->insert_id();
					$db->query("UPDATE ".STRUCTURE_TABLE." SET page_id='".$page_id."', rss='0' WHERE id='".$sid."'");
					break;
			}

			# пересчитываем "детей"
			$this->count_childs($post->parent_id);

			# уведомление
			$logger->info("Структурная еденица #".$sid." успешно добавлена.");

			# переход
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
	 * Функция редактирования элемента структуры
	 *
	 * @param int $sid - уникальный идентификатор структурной едеицы
	 *
	 * @return string
	 */
	private function edit_unit($sid) {

		global $db, $smarty, $tpl;

		$q = $db->query("SELECT id, parent_id, group_access, alias, title, meta_description, meta_keywords, noindex, sort, page_type, thumb_img_width, thumb_img_height FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
		$data = $db->fetch_assoc($q);

		# check group access
		if(trim($data['group_access']) != "0") {
			$gids = explode(",", $data['group_access']);
			$gids = array_flip($gids);
		}
		else $gids[0] = 0;

		# list groups
		$groups = [];
		$q = $db->query("SELECT gid, title, users FROM ".USERS_GROUP_TABLE." ORDER BY gid ASC");
		while($row = $db->fetch_assoc($q)) {
			$groups[] = $row;
		}

		# шаблонизируем... (слово то какое...)
		$smarty->assign("gids", $gids);
		$smarty->assign("groups", $groups);
		$smarty->assign("data", $data);

		return $tpl->load_template("structure_edit", true);
	}


	/**
	 * Обновляем элемент структуры
	 *
	 * @param $sid
	 *
	 * @internal param int $id - Идентификатор структурной еденицы
	 */
	private function update_unit($sid) {

		global $db, $logger, $post;

		# check unit parametrs
		$this->check_unit_parametrs();


		if(!isset($_SESSION['error'])) {
			$post->sort = round($post->sort);

			# Нельзя менять родителя у главной страницы и алиас
			If($sid == 1) {
				$post->parent_id = 0;
				$post->alias = "index";
			}

			# Если мы назначаем нового родителя
			if($post->parent_id != $post->now_parent_id) {

				# Проверим, что не пытаемся быть родителем самим себе
				if($post->parent_id == $sid) {
					$post->parent_id = $post->now_parent_id;
					$logger->error("Не удалось изменить иерархию! Вы не можете изменить иерархию директории назначив её родителем самой себе!");
				}
				# ... и что новый родитель это не наш ребенок
				else {
					$childs = $this->engine->load_tree($sid);

					foreach((array)$childs AS $v) {
						if($post->parent_id == $v['id']) {
							$post->parent_id = $post->now_parent_id;
							$logger->error("Не удалось изменить иерархию! Вы не можете изменить иерархию директории переместив её в свой дочерний элемент!");
						}
					}

				}
			}

			# проверяем тип родителя
			$q = $db->query("SELECT page_type FROM ".STRUCTURE_TABLE." WHERE id='".$post->parent_id."'");
			$p = $db->fetch_assoc($q);

			# проверяем тип текущей страницы
			$q = $db->query("SELECT page_type FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
			$n = $db->fetch_assoc($q);

			# Нельзя к лентам добавлять другие дочерние элементы, кроме таких же лент.
			if($p['page_type'] == "feed" && $n['page_type'] != "feed") {
				$logger->error("Вы не можете установить для ленты в качестве дочерней страницы другой структурный элемент, кроме ленты.");
				$post->parent_id = $post->now_parent_id;
			}

			# DB
			$db->query("UPDATE ".STRUCTURE_TABLE."
					SET
						alias='".$post->alias."',
						title='".$post->title."',
						parent_id='".$post->parent_id."',
						group_access='".$post->gids."',
						meta_description='".$post->meta_description."',
						meta_keywords='".$post->meta_keywords."',
						noindex='".$post->noindex."',
						sort='".$post->sort."',
						date_modified='".time()."',
						thumb_img_width='".$post->thumb_img_width."',
						thumb_img_height='".$post->thumb_img_height."'
					WHERE
						id='".$sid."'");

			# Если мы назначаем нового родителя
			if($post->parent_id != $post->now_parent_id) {
				# пересчитываем "детей"
				$this->count_childs($post->parent_id);
				$this->count_childs($post->now_parent_id);
			}

			# уведомление
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
	 * Удаляем структурный элемент
	 *
	 * @param $sid
	 *
	 * @internal param int $id
	 */
	private function delete_unit($sid) {

		global $db, $logger;

		$q = $db->query("SELECT childs, parent_id, page_id, page_type FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
		$c = $db->fetch_assoc($q);

		if($c['childs'] == 0) {

			switch($c['page_type']) {

				case 'html': # del content html
					require_once _ROOCMS."/acp/pages_html.php";
					$this->unit = new ACP_PAGES_HTML;
					$this->unit->delete($sid);
					break;

				case 'php': # del content php
					require_once _ROOCMS."/acp/pages_php.php";
					$this->unit = new ACP_PAGES_PHP;
					$this->unit->delete($sid);
					break;

				case 'feed': # del content feed
					$feeds_data = array(
						'id'			=> $this->engine->page_id,
						'alias'			=> $this->engine->page_alias,
						'title'			=> $this->engine->page_title,
						'rss'			=> $this->engine->page_rss,
						'items_per_page'	=> $this->engine->page_items_per_page,
						'items_sorting'		=> $this->engine->page_items_sorting,
						'thumb_img_width'	=> $this->engine->page_thumb_img_width,
						'thumb_img_height'	=> $this->engine->page_thumb_img_height
					);

					require_once _CLASS."/trait_feedExtends.php";
					require_once _ROOCMS."/acp/feeds_feed.php";
					$this->unit = new ACP_FEEDS_FEED($feeds_data);
					$this->unit->delete_feed($sid);
					break;
			}


			# structure unit
			$db->query("DELETE FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");


			# уведомление
			$logger->info("Страница #".$sid." успешно удалена");

			# recount parent childs
			$this->count_childs($c['parent_id']);
		}
		else {
			$logger->error("Невозможно удалить страницу, по причине имеющихся у страницы дочерних связей. Сначала перенесите или удалите дочерние страницы.");
		}

		# переход
		goback();
	}


	/**
	 * Проверяем "алиас" на уникальность
	 *
	 * ВНИМАНИЕ! Не расчитывайте на эту функцию, она временная.
	 *
	 * @param string $name    - алиас
	 * @param string $without - Выражение исключения для mysql запроса
	 *
	 * @return bool $res - true - если алиас не уникален, false - если алиас уникален
	 */
	private function check_alias($name, $without="") {

		global $db;

		$res = false;

		if(trim($without) != trim($name)) {

			$w = (trim($without) != "") ? "alias!='".$without."'" : "" ;

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
	 * Пересчитываем "детей"
	 *
	 * @param int $id
	 */
	private function count_childs($id) {

		global $db;

		$c = $db->count(STRUCTURE_TABLE, "parent_id='".$id."'");

		$db->query("UPDATE ".STRUCTURE_TABLE." SET childs='".$c."' WHERE id='".$id."'");
	}


	/**
	 * Функция проверяет алиас структурной еденицы и при необходимости корректирует его.
	 */
	private function processing_alias() {

		global $parse, $logger, $post;


		if(!isset($post->alias)) {
			if(isset($post->title)) {
				$post->alias = $post->title;
			}
			else {
				$logger->error("Не указан alias для структурной еденицы.");
			}
		}

		# предупреждаем возможные ошибки с алиасом структурной единицы
		if(isset($post->alias)) {

			$post->alias = $parse->text->transliterate($post->alias,"lower");

			# избавляем URI от возможных конвульсий
			$post->alias = strtr($post->alias, array(' '=>'_', '-'=>'_', '='=>'_'));

			# Чистим alias
			$post->alias = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array('_','_','_',''), $post->alias);

			# а так же проверяем что бы алиас не оказался числом
			$post->alias = $parse->text->correct_aliases($post->alias);
		}
	}


	/**
	 * Функция проверяет заголовок, алиас, разрешения и иные параметры
	 * перед размещением или обновлением структурной еденицы.
	 */
	private function check_unit_parametrs() {

		global $logger, $post, $img;

		# title
		if(!isset($post->title)) {
			$logger->error("Не указано название страницы.");
		}

		# alias
		$this->processing_alias();
		if(!isset($post->old_alias)) {
			$post->old_alias = "";
		}
		if(!$this->check_alias($post->alias, $post->old_alias)) {
			$logger->error("Алиас страницы не уникален.");
		}

		# group access
		if(isset($post->gids) && is_array($post->gids)) {
			$post->gids = implode(",", $post->gids);
		}
		else {
			$post->gids = 0;
		}

		# thumbnail check
		$img->check_post_thumb_parametrs();
	}
}

/**
 * Init Class
 */
$acp_structure = new ACP_Structure;