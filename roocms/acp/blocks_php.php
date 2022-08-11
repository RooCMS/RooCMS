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


class ACP_Blocks_PHP {

	/**
	 * Create block PHP
	 */
	public function create() {

		global $db, $tpl, $smarty, $post, $logger;

		if(isset($post->create_block)) {

			# check data post
			$this->check_block_parametrs();

			if(!isset($_SESSION['error'])) {
				$db->query("INSERT INTO ".BLOCKS_TABLE." (title, alias, content, date_create, date_modified, block_type)
								  VALUES ('".$post->title."', '".$post->alias."', '".$post->content."', '".time()."', '".time()."', 'php')");

				$bid = $db->insert_id();

				# notice
				$logger->info("Блок #".$bid." успешно добавлен!");

				# go
				go(CP."?act=blocks");
			}

			# go
			go(CP."?act=blocks&part=create&type=php");
		}

		$content = $tpl->load_template("blocks_create_php", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Edit block PHP
	 *
	 * @param int $id
	 */
	public function edit(int $id) {

		global $db, $tpl, $smarty;

		$q = $db->query("SELECT id, title, alias, content FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$data = $db->fetch_assoc($q);

		# tpl
		$smarty->assign("data",$data);
		$content = $tpl->load_template("blocks_edit_php", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Update block PHP
	 *
	 * @param int $id
	 */
	public function update(int $id) {

		global $db, $post, $get, $logger;

		if(isset($post->update_block)) {

			# check data post
			$this->check_block_parametrs();

			if($post->id != $get->_block) {
				$logger->error("Системная ошибка...");
			}

			if(!isset($_SESSION['error'])) {

				$db->query("UPDATE ".BLOCKS_TABLE."
							SET
								title='".$post->title."',
								alias='".$post->alias."',
								content='".$post->content."',
								date_modified='".time()."'
							WHERE
								id='".$id."'");

				# notice
				$logger->info("Блок #".$id." успешно обновлен!");
			}

			go(CP."?act=blocks");
		}

		# goback
		goback();
	}


	/**
	 * Remove block PHP
	 *
	 * @param int $id
	 */
	public function delete(int $id) {

		global $db, $logger;

		# query
		$db->query("DELETE FROM ".BLOCKS_TABLE." WHERE id='".$id."'");

		# notice
		$logger->info("Блок #".$id." успешно удален!");

		# go
		go(CP."?act=blocks");
	}


	/**
	 * Check Block Parametrs
	 */
	private function check_block_parametrs() {

		global $db, $parse, $post, $logger;

		if(!isset($post->title)) {
			$logger->error("Не указано название блока!", false);
		}

		if(!isset($post->alias)) {
			$logger->error("Не указан алиас блока!", false);
		}
		else {
			$post->alias = $parse->text->correct_aliases($post->alias);


			$check_alias = (isset($post->oldalias)) ? "alias!='".$post->oldalias."'" : "" ;

			if($db->check_id($post->alias, BLOCKS_TABLE, "alias", $check_alias)) {
				$logger->error("Алиас блока не уникален!", false);
			}
		}

		if(!isset($post->content)) {
			$logger->error("Пустое тело блока!", false);
		}

	}
}
