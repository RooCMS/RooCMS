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


class ACP_Blocks {

	private $unit;			# ... object for works content blocks

	private $block = 0;		# ID block
	private $types = array(	"html"	=> true,
				"php"	=> true);


	/**
	* Поехали
	*     (с) Гагарин
	*/
	public function __construct() {

		global $tpl;

		$this->init();
		$this->action();

		# выводим
		$tpl->load_template("blocks");
	}


	/**
	* Инициализация установки
	*/
	private function init() {

		global $db, $get;

		if(isset($get->_block) && $db->check_id($get->_block, BLOCKS_TABLE)) {
			$this->block = $get->_block;
			$q = $db->query("SELECT block_type FROM ".BLOCKS_TABLE." WHERE id='".$this->block."'");
			$t = $db->fetch_assoc($q);
			$get->_type = $t['block_type'];
		}

		if(isset($get->_type) && array_key_exists($get->_type, $this->types) && $this->types[$get->_type]) {
			switch($get->_type) {
				case 'html':
					require_once _ROOCMS."/acp/blocks_html.php";
					$this->unit = new ACP_Blocks_HTML;
					break;

				case 'php':
					require_once _ROOCMS."/acp/blocks_php.php";
					$this->unit = new ACP_Blocks_PHP;
					break;
			}
		}
	}


	/**
	* Определяем задачи для каждой цели
	*/
	private function action() {

		global $roocms;

		switch($roocms->part) {
			case 'create':
				$this->unit->create();
				break;

			case 'edit':
				$this->unit->edit($this->block);
				break;

			case 'update':
				$this->unit->update($this->block);
				break;

			case 'delete':
				$this->unit->delete($this->block);
				break;

			default:
				$this->view_all_blocks();
				break;
		}
	}


	/**
	* Видим все блоки
	*/
	private function view_all_blocks() {

		global $db, $tpl, $smarty;

		$data = [];
		$q = $db->query("SELECT id, alias, block_type, title FROM ".BLOCKS_TABLE." ORDER BY id ASC");
		while($row = $db->fetch_assoc($q)) {
			$data[] = $row;
		}

		$smarty->assign("data", $data);
		$content = $tpl->load_template("blocks_view_list", true);
		$smarty->assign("content", $content);
	}
}

/**
 * Init Class
 */
$acp_blocks = new ACP_Blocks;