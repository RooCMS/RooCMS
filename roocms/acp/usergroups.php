<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso.
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


# require trait
require_once "extends/trait_acp_useroperation.php";


/**
 * Class ACP_UserGroups
 */
class ACP_UserGroups {

	use ACP_UserOperation;

	/**
	 * ACP_Users constructor.
	 */
	public function __construct() {

		global $roocms, $tpl;


		# Check user id
		$this->check_var_uid();
		# Check group id
		$this->check_var_gid();


		# action
		switch($roocms->part) {

			case 'create_group':
				$this->create_new_group();
				break;

			case 'edit_group':
			case 'update_group':
			case 'delete_group':
				if($this->gid != 0) {
					switch($roocms->part) {
						case 'edit_group':
							$this->edit_group($this->gid);
							break;

						case 'update_group':
							$this->update_group($this->gid);
							break;

						case 'delete_group':
							$this->delete_group($this->gid);
							break;
					}
				}
				else {
					go(CP."?act=usergroups&part=group_list");
				}
				break;

			case 'exclude_user_group':
				if($this->uid != 0 && $this->gid != 0) {
					$this->exclude_user_group($this->uid, $this->gid);
				}
				goback();
				break;

			default:
				$this->view_all_groups();
				break;
		}

		# output
		$tpl->load_template("usergroups");
	}


	/**
	 * Show groups list
	 */
	private function view_all_groups() {

		global $db, $smarty, $tpl, $parse;

		$data = [];
		$q = $db->query("SELECT gid, title, users, date_create, date_update FROM ".USERS_GROUP_TABLE." ORDER BY gid");
		while($row = $db->fetch_assoc($q)) {

			$row['date_create'] = $parse->date->unix_to_rus($row['date_create'], false, true, false);
			$row['date_update'] = $parse->date->unix_to_rus($row['date_update'], false, true, false);

			$data[] = $row;
		}

		# tpl
		$smarty->assign("data", $data);
		$content = $tpl->load_template("usergroups_view_groups", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Create new group
	 */
	private function create_new_group() {

		global $db, $smarty, $tpl, $post, $logger;

		if(isset($post->create_group)) {

			# title
			if(!isset($post->title)) {
				$logger->error("У группы должно быть название!");
			}
			if(isset($post->title) && $db->check_id($post->title, USERS_GROUP_TABLE, "title")) {
				$logger->error("Группа с таким название уже существует");
			}

			if(!isset($_SESSION['error'])) {

				$db->query("INSERT INTO ".USERS_GROUP_TABLE." (title, date_create, date_update)
								       VALUES ('".$post->title."', '".time()."', '".time()."')");
				$gid = $db->insert_id();

				# notice
				$logger->info("Группа #".$gid." была успешно создана.");

				# go
				if(isset($post->create_group['ae'])) {
					go(CP."?act=usergroups&part=group_list");
				}

				go(CP."?act=usergroups&part=edit_group&gid=".$gid);
			}

			goback();
		}


		# tpl
		$content = $tpl->load_template("usergroups_create_new_group", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Edit group data
	 *
	 * @param int $gid - group identificator.
	 */
	private function edit_group(int $gid) {

		global $db, $smarty, $tpl;

		$q = $db->query("SELECT gid, title FROM ".USERS_GROUP_TABLE." WHERE gid='".$gid."'");
		$group = $db->fetch_assoc($q);

		$guser = [];
		$u = $db->query("SELECT uid, nickname, login, avatar, status, ban FROM ".USERS_TABLE." WHERE gid='".$gid."' ORDER BY uid");
		while($row = $db->fetch_assoc($u)) {
			$guser[$row['uid']] = $row;
		}

		# tpl
		$smarty->assign("group", $group);
		$smarty->assign("users", $guser);
		$content = $tpl->load_template("usergroups_edit_group", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Update group data
	 *
	 * @param int $gid - group identificator
	 */
	private function update_group(int $gid) {

		global $db, $post, $users, $logger;

		if(isset($post->update_group)) {

			$q = $db->query("SELECT title FROM ".USERS_GROUP_TABLE." WHERE gid='".$gid."'");
			$gdata = $db->fetch_assoc($q);

			$query = "";

			# login
			if(isset($post->title)) {
				if(!$users->check_field("title", $post->title, $gdata['title'], USERS_GROUP_TABLE)) {
					$logger->error("Название группы не может совпадать с названием другой группы!");
				}
				else {
					$query .= "title='".$post->title."', ";
				}
			}
			else {
				$logger->error("У группы должно быть название.");
			}

			# update
			if(!isset($_SESSION['error'])) {

				# update
				$db->query("UPDATE ".USERS_GROUP_TABLE." SET ".$query." date_update='".time()."' WHERE gid='".$gid."'");
				$this->count_users($gid);

				# notice
				$logger->info("Данные группы #".$gid." успешно обновлены.");

				# go
				if(isset($post->update_group['ae'])) {
					go(CP."?act=usergroups&part=group_list");
				}

				go(CP."?act=usergroups&part=edit_group&gid=".$gid);
			}
		}

		# goback
		goback();

	}


	/**
	 * Remove group
	 *
	 * @param int $gid - group identificator
	 */
	private function delete_group(int $gid) {

		global $db, $logger;

		$db->query("UPDATE ".USERS_TABLE." SET gid='0' WHERE gid='".$gid."'");

		$db->query("DELETE FROM ".USERS_GROUP_TABLE." WHERE gid='".$gid."'");
		$logger->info("Группа #".$gid." была удалена из Базы Данных.");

		# go
		goback();
	}


	/**
	 * Exclude user from group
	 *
	 * @param int $uid - user identificator
	 * @param int $gid - group identificator
	 */
	private function exclude_user_group(int $uid, int $gid) {

		global $db, $logger;

		$q = $db->query("SELECT gid FROM ".USERS_TABLE." WHERE uid='".$uid."'");
		$data = $db->fetch_assoc($q);

		if($data['gid'] == $gid) {
			$db->query("UPDATE ".USERS_TABLE." SET gid='0' WHERE uid='".$uid."'");

			# notice
			$logger->info("Пользователь #".$uid." был успешно исключен из группы #".$gid.".");

			# recount user in group
			$this->count_users($gid);
		}

		# go
		goback();
	}
}

/**
 * Init class
 */
$acp_usergroups = new ACP_UserGroups;
