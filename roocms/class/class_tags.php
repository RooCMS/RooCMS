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
 * Class Tags
 */
class Tags {


	/**
	 * Функция возвращает ввиде массива полный список тегов
	 *
	 * @param bool $with_zero - Если флаг true, то вернет список тегов включая, нулевые значения. Иначе вернет только используемые теги.
	 * @param int  $limit     - Кол-во тегов (срез) которые вернет запрос
	 *
	 * @return array
	 */
	public function list_tags(bool $with_zero=false, int $limit=0) {

		global $db;

		# condition
		$cond = "amount != '0' ";
		if($with_zero) {
			$cond = " amount > '0' ";
		}

		# limit condition
		$lcond = "";
		if($limit > 0) {
			$lcond = " LIMIT 0,".$limit;
		}

		# query
		$tags = [];
		$q = $db->query("SELECT title, amount FROM ".TAGS_TABLE." WHERE ".$cond." ORDER BY amount DESC ".$lcond);
		while($data = $db->fetch_assoc($q)) {
			$tags[] = $data;
		}

		return $tags;
	}


	/**
	 * Функция собирает теги объекта в строку разделенные запятыми.
	 *
	 * @param string|array $linkedto - ссылка на объект
	 *
	 * @return array
	 */
	public function read_tags($linkedto) {

		global $db;

		if(!is_array($linkedto)) {
			$linkedto = array($linkedto);
		}

		# create condition
		$cond = "";

		foreach($linkedto AS $value) {
			$cond = $db->qcond_or($cond);
			$cond .= " l.linkedto='".$value."' ";
		}


		if($cond != "") {
			$cond = " (".$cond.")";
		}
		else {
			$cond .= " l.linkedto='0' ";
		}


		$tags = [];
		$q = $db->query("SELECT l.tag_id, t.title, t.amount, l.linkedto FROM ".TAGS_LINK_TABLE." AS l LEFT JOIN ".TAGS_TABLE." AS t ON (t.id = l.tag_id) WHERE".$cond." ORDER BY t.title");
		while($data = $db->fetch_assoc($q)) {
			$tags[] = $data;
		}

		# return
		return $tags;
	}


	/**
	 * Функция собирает теги к объектам лент
	 *
	 * @param array $resarray - выходной массив, к которому добавляются теги
	 * @param array $taglinks - массив с ссылками на теги по которому осуществляется сбор
	 *
	 * @return array
	 */
	public function collect_tags(array $resarray, array $taglinks) {

		if(!empty($taglinks)) {
			$alltags = $this->read_tags($taglinks);
			foreach((array)$alltags AS $value) {
				$lid = explode("=",$value['linkedto']);
				$resarray[$lid[1]]['tags'][] = array("tag_id"=>$value['tag_id'], "title"=>$value['title']);
			}
		}

		return $resarray;
	}


	/**
	 * Save tags in bd
	 *
	 * @param string $tags     - tags (separeted: comma)
	 * @param string $linkedto - link to element
	 */
	public function save_tags(string $tags, string $linkedto) {

		global $db;

		# get element tags
		$now_tags = array_map(array($this, "get_tag_title"), $this->read_tags($linkedto));

		# handle saved tags
		$new_tags = $this->parse_tags($tags);

		# Compare old and new tags, manipulate.
		$tags = $this->diff_tag($now_tags, $new_tags, $linkedto);

		# if have tags
		if(!empty($tags)) {
			$v = $db->check_array_ids($tags, TAGS_TABLE, "title");

			foreach($tags AS $value) {
				if($v[$value]['check']) {
					# add link to used tag
					$this->add_instock_tag($v[$value]['id_value'], $linkedto);
				}
				else {
					# create  new tag and linked
					$this->add_new_tag($v[$value]['value'], $linkedto);
				}
			}
		}
	}


	/**
	 * Функция проводит сравнении данных в массивах определя какие теги трогать, как пропустить
	 *
	 * @param array  $now      - массив с имеющимися у объекта тегами
	 * @param array  $new      - массив с новыми тегами
	 * @param string $linkedto - ссылка на объект
	 *
	 * @return array
	 */
	public function diff_tag(array $now, array $new, string $linkedto) {

		$tags = [];

		if(empty($new)) {     // Если теги удалили...
			$this->remove_tags($now, $linkedto);
		}
		elseif(empty($now)) { // Если тегов не было, то дальнейшие обработки не нужны. Возвращаем список новых тегов.
			$tags = $new;
		}
		else {                // Если в массивах есть что, проводим сравнение
			$tags = $new;

			# массив для устаревших тегов
			$old = [];

			foreach($now AS $value) {
				if(!in_array($value, $new)) {
					$old[] = $value;
				}

				if(($k = array_search($value, $new)) !== false) {
					unset($tags[$k]);
				}
			}

			# Удаляем ненужные теги
			$this->remove_tags($old, $linkedto);
		}

		# возвращаем список новых/обновленных тегов
		return $tags;
	}


	/**
	 * Add new tag in DB
	 *
	 * @param string $tag      - tag
	 * @param string $linkedto - tag pointer
	 */
	private function add_new_tag(string $tag, string $linkedto) {

		global $db;

		# create
		$db->query("INSERT INTO ".TAGS_TABLE." (title, amount) VALUES ('".$tag."', '1')");
		$tag_id = $db->insert_id();

		# linked
		$this->add_instock_tag($tag_id, $linkedto);
	}


	/**
	 * Add tag pointer.
	 *
	 * @param int    $tag_id   - tag id
	 * @param string $linkedto - tag pointer
	 */
	private function add_instock_tag(int $tag_id, string $linkedto) {

		global $db;

		# linked
		$db->query("INSERT INTO ".TAGS_LINK_TABLE." (tag_id, linkedto) VALUES ('".$tag_id."', '".$linkedto."')");

		# recount
		$this->recount_tag($tag_id);
	}


	/**
	 * Функция удаляет теги у заданного объекта.
	 *
	 * @param array|string $tags     - массив или строка с тегами
	 * @param string       $linkedto - ссылка на объект
	 */
	private function remove_tags($tags, string $linkedto) {

		global $db;

		# Если получили строку, преобразуем ее в массив.
		if(!is_array($tags)) {
			$tags = $this->parse_tags($tags);
		}

		# if have tags
		if(!empty($tags)) {

			# составляем условие
			$cond1 = "";
			foreach($tags AS $value) {
				$cond1 = $db->qcond_or($cond1);
				$cond1 .= "title='".$value."'";
			}

			# get tag id for condition unlinks
			$tr = [];
			$cond2 = "";
			$q = $db->query("SELECT id FROM ".TAGS_TABLE." WHERE ".$cond1);
			while($data = $db->fetch_assoc($q)) {
				$cond2 = $db->qcond_or($cond2);
				$cond2 .= "tag_id='".$data['id']."'";

				$tr[] = $data['id'];
			}
			$cond2 = "(".$cond2.")";

			# unlinked
			$db->query("DELETE FROM ".TAGS_LINK_TABLE." WHERE ".$cond2." AND linkedto='".$linkedto."'");

			# recount
			foreach($tr AS $v) {
				$this->recount_tag($v);
			}
		}
	}


	/**
	 * Функция парсит и форматирует строку с тегами разделенными запятыми и преобразует её в массив.
	 *
	 * @param string $tags - строка с тегами разделенными запятой
	 *
	 * @return array возврашает массив с тегами
	 */
	private function parse_tags(string $tags) {

		global $parse;

		# check
		$strtag = array_unique($parse->check_array(explode(",",mb_strtolower($tags))));

		$tag = [];
		foreach($strtag as $value) {
			if(trim($value) != "") {
				# чистим от мусорных символов
				$tag[] = $parse->clear_string($value);
			}
		}

		return $tag;
	}


	/**
	 * Recount tag
	 *
	 * @param int $tag_id - tag id
	 * @param int $amount - now amount tag
	 */
	private function recount_tag(int $tag_id, int $amount=-1) {

		global $db;

		# calculate
		$c = $db->count(TAGS_LINK_TABLE, "tag_id='".$tag_id."'");

		# update if quantity changed
		if(round($amount) != $c) {
			$db->query("UPDATE ".TAGS_TABLE." SET amount='".$c."' WHERE id='".$tag_id."'");
		}
	}


	/**
	 * Function for array_map, to callback title tag
	 *
	 * @param $tag
	 *
	 * @return mixed
	 */
	static public function get_tag_title($tag) {

		return $tag['title'];
	}
}
