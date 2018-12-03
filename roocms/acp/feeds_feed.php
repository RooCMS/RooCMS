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


class ACP_Feeds_Feed {

	use FeedExtends;

	# vars
	private $feed     = [];	# structure parametrs
	private $userlist = [];



	/**
	 * Key on "start" (c)
	 *
	 * @param $structure_data
	 */
	public function __construct($structure_data) {
		$this->feed =& $structure_data;
	}


	/**
	 * feed view
	 */
	public function control() {

		global $db, $parse, $tags, $tpl, $smarty;

		# order request
		$order = $this->feed_order($this->feed['items_sorting']);

		$smarty->assign("feed", $this->feed);


		# feed items
		$taglinks = [];
		$feedlist = [];
		$q = $db->query("SELECT id, status, title, brief_item, date_publications, date_end_publications, date_update, views FROM ".PAGES_FEED_TABLE." WHERE sid='".$this->feed['id']."' ORDER BY ".$order);
		while($row = $db->fetch_assoc($q)) {

			$row['publication_status'] = ($row['date_end_publications'] < time() && $row['date_end_publications'] != 0) ? "hide" : "show" ;

			$row['date_publications'] = $parse->date->unix_to_rus($row['date_publications']);

			if($row['date_end_publications'] != 0) {
				$row['date_end_publications'] = $parse->date->unix_to_rus($row['date_end_publications']);
			}

			$row['date_update'] = $parse->date->unix_to_rus($row['date_update'], false, true, true);

			$taglinks[$row['id']] = "feeditemid=".$row['id'];
			$feedlist[$row['id']] = $row;
		}


		# tags collect
		$feedlist = $tags->collect_tags($feedlist, $taglinks);

		# smarty
		$smarty->assign("subfeeds", $this->feed['subfeeds']);
		$smarty->assign("feedlist", $feedlist);

		$content = $tpl->load_template("feeds_control_feed", true);
		$smarty->assign("content",  $content);
	}


	/**
	 * Create new record to feed
	 */
	public function create_item() {

		global $db, $users, $logger, $tags, $files, $img, $post, $tpl, $smarty;

		# insert db
		if(isset($post->create_item)) {

			# Check post data
			$this->check_post_data_fields();
			$this->control_post_data_date();

			if(!isset($_SESSION['error'])) {

				# Check secondary fields
				$this->correct_post_fields();

				# insert
				$db->query("INSERT INTO ".PAGES_FEED_TABLE." (title, meta_title, meta_description, meta_keywords,
									      brief_item, full_item, author_id,
									      date_create, date_update, date_publications, date_end_publications,
									      sort, sid)
								      VALUES ('".$post->title."', '".$post->meta_title."', '".$post->meta_description."', '".$post->meta_keywords."',
									      '".$post->brief_item."', '".$post->full_item."', '".$post->author_id."',
									      '".time()."', '".time()."', '".$post->date_publications."', '".$post->date_end_publications."',
									      '".$post->itemsort."', '".$this->feed['id']."')");

				# get feed item id
				$fiid = $db->insert_id();

				# save tags
				$tags->save_tags($post->tags, "feeditemid=".$fiid);


				# attachment images
				$images = $img->upload_image("images", "", array($this->feed['thumb_img_width'], $this->feed['thumb_img_height']));
				if($images) {
					foreach($images AS $image) {
						$img->insert_images($image, "feeditemid=".$fiid, $post->title);
					}
				}

				# attachment files
				$files->upload("files", "feeditemid=".$fiid);

				# recount items
				$this->count_items($this->feed['id']);

				# notice
				$logger->info("Запись #".$fiid." <".$post->title."> успешно создана.");

				// TODO: Переделать!
				# mailling
				$this->mailing($fiid, $post->title,$post->brief_item, $post->force);
			}

			# go
			go(CP."?act=feeds&part=control&page=".$this->feed['id']);
		}

		# userlist
		$this->userlist = $users->get_userlist();

		# popular tags
		$poptags = $tags->list_tags(true, 20);

		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");

		# smarty vars
		$smarty->assign("feed",     $this->feed);      # feed data
		$smarty->assign("poptags",  $poptags);         # tags
		$smarty->assign("userdata", $users->userdata); # users
		$smarty->assign("userlist", $this->userlist);  # users

		# tpl
		$content = $tpl->load_template("feeds_create_item_feed", true);
		$smarty->assign("content",  $content);
	}


	/**
	 * Edit record from feed
	 *
	 * @param int $id - record identificator from feed
	 */
	public function edit_item($id) {

		global $db, $users, $tags, $files, $img, $tpl, $smarty, $parse;

		# userlist
		$this->userlist = $users->get_userlist();

		# get data
		$q = $db->query("SELECT id, sid, status, sort, title, meta_title, meta_description, meta_keywords, brief_item, full_item, author_id, date_publications, date_end_publications FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$item = $db->fetch_assoc($q);


		$item['date_publications'] = $parse->date->unix_to_rusint($item['date_publications']);

		if($item['date_end_publications'] != 0) {
			$item['date_end_publications'] = $parse->date->unix_to_rusint($item['date_end_publications']);
		}

		# item tags
		$item['tags'] = implode(", ", array_map(array("Tags", "get_tag_title"), $tags->read_tags("feeditemid=".$id)));


		$smarty->assign("item",$item);

		# popular tags
		$poptags = $tags->list_tags(true, 15);


		# download attached images
		$attachimg = $img->load_images("feeditemid=".$id);
		$smarty->assign("attachimg", $attachimg);

		# show attached images
		$attachedimages = $tpl->load_template("attached_images", true);
		$smarty->assign("attachedimages", $attachedimages);


		# download attached files
		$attachfile = $files->load_files("feeditemid=".$id);
		$smarty->assign("attachfile", $attachfile);

		# show attached files
		$attachedfiles = $tpl->load_template("attached_files", true);
		$smarty->assign("attachedfiles", $attachedfiles);


		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");

		# smarty vars
		$smarty->assign("feed",     $this->feed);      # feed data
		$smarty->assign("poptags",  $poptags);         # tags
		$smarty->assign("userdata", $users->userdata); # users
		$smarty->assign("userlist", $this->userlist);  # users

		# tpl
		$content = $tpl->load_template("feeds_edit_item_feed", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Update record
	 *
	 * @param int $id - record identificator from feed
	 */
	public function update_item($id) {

		global $db, $logger, $tags, $files, $img, $post, $get;

		# Проверяем вводимые поля на ошибки
		$this->check_post_data_fields();
		$this->control_post_data_date();

		# update
		if(!isset($_SESSION['error'])) {

                        # Check secondary fields
			$this->correct_post_fields();

			# update
		        $db->query("UPDATE ".PAGES_FEED_TABLE."
		        		SET
		        			status = '".$post->status."',
		        			sort = '".$post->itemsort."',
						title = '".$post->title."',
						meta_title = '".$post->meta_title."',
						meta_description = '".$post->meta_description."',
						meta_keywords = '".$post->meta_keywords."',
						brief_item = '".$post->brief_item."',
						full_item = '".$post->full_item."',
						date_publications = '".$post->date_publications."',
						date_end_publications = '".$post->date_end_publications."',
						date_update = '".time()."',
						author_id = '".$post->author_id."'
					WHERE
						id = '".$id."'");

			# save tags
			$tags->save_tags($post->tags, "feeditemid=".$id);

			# notice
			$logger->info("Запись #".$id." <".$post->title."> успешна отредактирована.");

			# update images
			$img->update_images_info("feeditemid", $id);

			# attachment images
			$images = $img->upload_image("images", "", array($this->feed['thumb_img_width'], $this->feed['thumb_img_height']));
			if($images) {
				foreach($images AS $image) {
					$img->insert_images($image, "feeditemid=".$id, $post->title);
				}
			}

			# attachment files
			$files->upload("files", "feeditemid=".$id);

			# go
			go(CP."?act=feeds&part=control&page=".$get->_page);
		}
		else {
			# back
			goback();
		}
	}


	/**
	 * Migrate record to another feed
	 *
	 * @param int $id - record identificator from feed
	 */
	public function migrate_item($id) {

		global $db, $logger, $tpl, $smarty, $post;

		# Migrate
		if(isset($post->from) && isset($post->to) && $db->check_id($post->from, STRUCTURE_TABLE, "id", "page_type='feed'") && $db->check_id($post->to, STRUCTURE_TABLE, "id", "page_type='feed'")) {

			$db->query("UPDATE ".PAGES_FEED_TABLE."
		        		SET
		        			sid = '".$post->to."',
						date_update = '".time()."'
					WHERE
						id = '".$id."'");

			# recount items
			$this->count_items($post->from);
			$this->count_items($post->to);


			# notice
			$logger->info("Элемент #".$id." успешно перемещен.");

			#go
			go(CP."?act=feeds&part=control&page=".$post->to);
		}


		# get data item from db
		$q = $db->query("SELECT id, sid, title FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$data = $db->fetch_assoc($q);

		# smarty vars
		$smarty->assign("item", $data);


		# get data feeds from db
		$feeds = [];
		$q = $db->query("SELECT id, title, alias FROM ".STRUCTURE_TABLE." WHERE page_type='feed' ORDER BY id ASC");
		while($row = $db->fetch_assoc($q)) {
			$feeds[$row['id']] = $row;
		}

		# smarty vars
		$smarty->assign("feeds", $feeds);

		# tpl
		$content = $tpl->load_template("feeds_migrate_item_feed", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Change status to record feed
	 *
	 * @param int $id - record id
	 * @param int $status - 1=show , 2=hide
	 */
	public function change_item_status($id, $status = 1) {

		global $db, $logger;

		$status = round($status);
		if($status >= 2 || $status < 0) {
			$status = 1;
		}

		# update data in db
		$db->query("UPDATE ".PAGES_FEED_TABLE." SET status='".$status."' WHERE id='".$id."'");

		# notice
		$mstatus = ($status == 1) ? "Видимый" : "Скрытый" ;
		$logger->info("Элемент #".$id." успешно изменил свой статус на <".$mstatus.">.");

		# go
		goback();
	}


	/**
	 * Remove record from feed
	 *
	 * @param int $id - record id
	 */
	public function delete_item($id) {

		global $db, $logger, $img, $tags;

		# get sid
		$q = $db->query("SELECT sid FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$row = $db->fetch_assoc($q);

		# del attached images
		$img->remove_images("feeditemid=".$id);

		# del tags
		$tags->save_tags("", "feeditemid=".$id);

		# delete item
		$db->query("DELETE FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");

		# recount items
		$this->count_items($row['sid']);

		# notice
		$logger->info("Элемент #".$id." успешно удален.");

		# go
		goback();
	}


	/**
	 * Remove feed
	 *
	 * @param int $sid - structure element id
	 */
	public function delete_feed($sid) {

		global $db, $img, $tags;

		$cond = "";
		$f = $db->query("SELECT id FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
		while($fid = $db->fetch_assoc($f)) {
			# del tags
			$tags->save_tags("", "feeditemid=".$fid['id']);
			# cond
			$cond .= (trim($cond) != "") ? " OR attachedto='feeditemid=".$fid['id']."' " :  " attachedto='feeditemid=".$fid['id']."' " ;
		}

		# del attached images
		if(trim($cond) != "") {
                	$img->remove_images($cond, true);
		}

		$db->query("DELETE FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
	}


	/**
	 * Settings feed
	 */
	public function settings() {

		global $db, $config, $tpl, $smarty, $get;

		if($db->check_id($get->_page, STRUCTURE_TABLE, "id", "page_type='feed'")) {

			$feed =& $this->feed;

			# Уведомление о глобальном отключении RSS лент
			$feed['rss_warn'] = (!$config->rss_power) ? true : false ;

			# глобальное значение количества элементов на страницу
			$feed['global_items_per_page'] =& $config->feed_items_per_page;

			# default thumb size
			$default_thumb_size = array('width'	=> $config->gd_thumb_image_width,
						    'height'	=> $config->gd_thumb_image_height);

			# smarty vars
			$smarty->assign("feed",$feed);
			$smarty->assign("default_thumb_size", $default_thumb_size);

			# tpl
			$content = $tpl->load_template("feeds_settings_feed", true);
			$smarty->assign("content", $content);
		}
		else {
			goback();
		}
	}


	/**
	 * Settings update to feed
	 */
	public function update_settings() {

		global $db, $img, $post, $logger;

		if(isset($post->update_settings)) {
			# update buffer
			$update = "";

			# RSS flag
			$rss = 0;
			if($post->rss == "1") {
				$rss = 1;
			}
			$update .= " rss='".$rss."', ";

			# items per page
			$items_per_page = 0;
			if(round($post->items_per_page) >= 0) {
				$items_per_page = round($post->items_per_page);
			}
			$update .= " items_per_page='".$items_per_page."', " ;

			# thumbnail check
			$img->check_post_thumb_parametrs();

			# items sorting in feed
			$items_sorting = "datepublication";
			if(isset($post->items_sorting)) {
				switch($post->items_sorting) {
					case 'title_asc':
						$items_sorting = "title_asc";
						break;

					case 'title_desc':
						$items_sorting = "title_desc";
						break;

					case 'manual_sorting':
						$items_sorting = "manual_sorting";
						break;
				}
			}
			$update .= " items_sorting = '".$items_sorting."', ";

			# show_child_feeds
			$show_child_feeds = "none";
			if(isset($post->show_child_feeds)) {
				switch($post->show_child_feeds) {
					case 'default':
						$show_child_feeds = "default";
						break;

					case 'forced':
						$show_child_feeds = "forced";
						break;
				}
			}


			# up data to db
			$db->query("UPDATE ".STRUCTURE_TABLE."
					SET
						".$update."
						show_child_feeds='".$show_child_feeds."',
						thumb_img_width='".$post->thumb_img_width."',
						thumb_img_height='".$post->thumb_img_height."',
						append_info_before='".$post->append_info_before."',
						append_info_after='".$post->append_info_after."',
						date_modified='".time()."'
					WHERE
						id='".$this->feed['id']."'");

			$logger->info("Настройки ленты #".$this->feed['id']." успешно обновлены.");
		}

		# go
		goback();
	}


	/**
	 * Recount records in feed
	 *
	 * @param int $sid - feed id
	 */
	public function count_items($sid) {

		global $db;

		# count
		$items = $db->count(PAGES_FEED_TABLE, "sid='".$sid."'");

		# save
		$db->query("UPDATE ".STRUCTURE_TABLE." SET items='".$items."' WHERE id='".$sid."'");
	}


	/**
	 * Check data post
	 */
	private function check_post_data_fields() {

		global $post, $logger;

		# title
		if(!isset($post->title)) {
			$logger->error("Не заполнен заголовок элемента", false);
		}

		# full desc item
		if(!isset($post->full_item)) {
			$logger->error("Не заполнен подробный текст элемента", false);
		}

		# status
		if(!isset($post->status) || $post->status >= 2) {
			$post->status = 1;
		}
	}


	/**
	 *  Check dates from data post
	 */
	private function control_post_data_date() {

		global $post, $parse;

		# check isset date publication
		if(!isset($post->date_publications)) {
			$post->date_publications = date("d.m.Y",time());
		}

		# check isset date end publication
		if(!isset($post->date_end_publications)) {
			$post->date_end_publications = 0;
		}

		# date publications
		$post->date_publications = $parse->date->rusint_to_unix($post->date_publications);

		# date end publications
		if($post->date_end_publications != 0) {
			$post->date_end_publications = $parse->date->rusint_to_unix($post->date_end_publications);
		}

		if($post->date_end_publications <= $post->date_publications) {
			$post->date_end_publications = 0;
		}
	}


	/**
	 * Check and correct secondary fields
	 */
	private function correct_post_fields() {

		global $users, $post;

		# tags
		if(!isset($post->tags)) {
			$post->tags = NULL;
		}

		# sort
		if(!isset($post->itemsort) || round($post->itemsort) < 0) {
			$post->itemsort = 0;
		}
		else {
			$post->itemsort = round($post->itemsort);
		}

		# userlist
		$this->userlist = $users->get_userlist();

		# author
		if(!isset($post->author_id) || !array_key_exists($post->author_id, $this->userlist)) {
			$post->author_id = 0;
		}
	}


	// TODO: Переделать!
	/**
	 * Это временная функция
	 *
	 * @param int    $id
	 * @param string $title
	 * @param string $subject
	 * @param int    $force
	 */
	private function mailing($id, $title, $subject, $force=-1) {

		global $users, $logger, $parse, $site;

		if($force != -1) {

			if($force == 1) {
				# all
				$userlist = $users->get_userlist(1, 0, -1, NULL, true);
			}
			else {
				# только подписчики
				$userlist = $users->get_userlist(1, 0, 1, NULL, true);
			}

			# html
			$subject = $parse->text->html($subject);
			$subject = "<h1>".$title."</h1>
					".$subject."
					<br /><br /><a href='".$site['domain']."/index.php?page=".$this->feed['alias']."&id=".$id."'>Читать полностью</a>";

			$log = "";
			foreach($userlist AS $val) {

				# send
				sendmail($val['email'], $title, $subject);

				# log
				$log .= " ".$val['email'];
			}

			$logger->info("Новость отправлена по адресам: ".$log);
		}
	}
}