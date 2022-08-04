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
 * Class Images
 */
class Images extends GD {


	/**
	 * Function for upload image
	 *
	 * @param string $file      - title vars in array $_FILES
	 * @param string $prefix    - prefix for filename
	 * @param array  $thumbsize - array(width,height) - thumbnail size
	 * @param bool   $watermark - on/off watermark
	 * @param bool   $modify    - on/off modify for resize and create thumbnail
	 * @param bool   $noresize  - on/off resize for unmodify image
	 * @param string $fname     - special filename
	 * @param string $path      - path for image folder
	 *
	 * @return false|array - filenames array or false if images dont upload
	 */
	public function upload_image(string $file, string $prefix="", array $thumbsize=[], bool $watermark=true, bool $modify=true, bool $noresize=false, string $fname="", string $path=_UPLOADIMAGES) {
		return $this->upload_post_image($file, $prefix, $thumbsize, $watermark, $modify, $noresize, $fname, $path);
	}


	/**
	 * Upload images with $_POST
	 *
	 * @param string $file      - title vars in array $_FILES
	 * @param string $prefix    - prefix for filename
	 * @param array  $thumbsize - array(width,height) - thumbnail size
	 * @param bool   $watermark - on/off watermark
	 * @param bool   $modify    - on/off modify for resize and create thumbnail
	 * @param bool   $noresize  - on/off resize for unmodify image
	 * @param string $fname     - special filename
	 * @param string $path      - path for image folder
	 *
	 * @return false|array - filenames array or false if images dont upload
	 */
	protected function upload_post_image(string $file, string $prefix="", array $thumbsize=[], bool $watermark=true, bool $modify=true, bool $noresize=false, string $fname="", string $path=_UPLOADIMAGES) {

		global $config, $files;

		# If lie
		if(!isset($_FILES[$file])) {
			return false;
		}

		# output array
		$images = [];

		# array for allowed file extension
		static $allow_exts = [];
		if(empty($allow_exts)) {
			$allow_exts = $this->get_allow_images();
		}

		# Set thumbnail size
		$this->set_mod_sizes($thumbsize);

		# handle $_FILES
		$upfiles = [];
		if(!is_array($_FILES[$file]['tmp_name'])) {
                	foreach($_FILES[$file] AS $k=>$v) {
				$upfiles[$file][$k][$file] = $v;
                	}
		}
		else {
			$upfiles[$file] = $_FILES[$file];
		}

		# proceed to processing
		foreach($upfiles[$file]['tmp_name'] AS $key=>$value) {
			if(isset($upfiles[$file]['tmp_name'][$key]) && $upfiles[$file]['error'][$key] == 0) {

				$upload = false;

				# lets work
				if(array_key_exists($upfiles[$file]['type'][$key], $allow_exts)) {

					# file extension
					$ext = $allow_exts[$upfiles[$file]['type'][$key]];

					# Set file name if set handly naming
					if($fname != "") {
						$upfiles[$file]['name'][$key] = $fname.".".$ext;
					}

					# Create file name
					$filename = $files->create_filename($upfiles[$file]['name'][$key], $prefix, "", $path);

					# filename pofix for "modify"/"nomodify" images
					$filename_pofix = "";
					if($modify) {
						$filename_pofix = "_original";
					}

					# save image on disk
					copy($upfiles[$file]['tmp_name'][$key], $path."/".$filename.$filename_pofix.".".$ext);

					# if uploading was successful and file exists
					$upload = is_file($path."/".$filename.$filename_pofix.".".$ext);
				}

				# if uploading was successful
				if($upload) {
					# convert jpgtowebp
					if($config->gd_convert_jpg_to_webp) {
						$ext = $this->convert_jpgtowebp($filename, $ext, $path);
					}

					$this->modify_image($filename, $ext, $path, $watermark, $modify, $noresize);
				}
				else {
					# TODO: Обработчик если загрузка не удалась =)
					$filename = false;
				}
			}
			else {
				# TODO: вписать сообщение об ошибке.
				# TODO: впрочем ещё надо и обработчик ошибок написать.
				$filename = false;
			}

			if($filename) {
				$images[] = $filename.".".$ext;
			}
		}

		# return filenames array
		return (count($images) > 0) ? $images : false ;
	}


	/**
	 * Load images
	 *
	 * @param string $cond  - image link condition
	 * @param int    $from  - start position for image load
	 * @param int    $limit - limit for uploading
	 *
	 * @return array $data - data array.
	 */
	public function load_images(string $cond, int $from = 0, int $limit = 0) {

                global $db;

		$data = [];

		$l = ($limit != 0) ? "LIMIT {$from},{$limit}" : "" ;

		$q = $db->query("SELECT id, filename, fileext, sort, alt FROM ".IMAGES_TABLE." WHERE attachedto='{$cond}' ORDER BY sort ".$l);
		while($image = $db->fetch_assoc($q)) {
			$image['original']	= $image['filename']."_original.".$image['fileext'];
			$image['resize']	= $image['filename']."_resize.".$image['fileext'];
			$image['thumb']		= $image['filename']."_thumb.".$image['fileext'];

			$data[] = $image;
		}

		return $data;
	}


	/**
	 * Upload image information to DB
	 *
	 * @param string $filename - filename without $pofix
	 * @param mixed  $attached - file parent
	 * @param string $alt      - alt-text
	 */
	public function insert_images(string $filename, $attached, string $alt="") {

        	global $db, $logger;

		$image = pathinfo($filename);

		$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename, fileext, alt)
						    VALUES ('".$attached."', '".$image['filename']."', '".$image['extension']."', '".$alt."')");

		# log
		$logger->log("Изображение ".basename($filename)." успешно загружено на сервер");
	}


	/**
	 * Update image info in DB
	 *
	 * @param mixed $attachedto - attached link image
	 * @param int   $id         - image id
	 */
	public function update_images_info($attachedto, int $id) {

		global $db, $post, $img, $parse;

		if(isset($post->sort) || isset($post->alt)) {
			$sortimg = $img->load_images($attachedto."=".$id);
			foreach($sortimg AS $v) {

				$cond = [];

				if(isset($post->sort[$v['id']]) && $post->sort[$v['id']] != $v['sort']) {
					$cond[] = "sort='".$post->sort[$v['id']]."'";
				}

				if(!isset($post->alt[$v['id']])) {
					$post->alt[$v['id']] = "";
				}

				if($post->alt[$v['id']] != $v['alt']) {
					$cond[] = "alt='".$post->alt[$v['id']]."'";
				}

				if(!empty($cond)) {
					# db query
					$db->query("UPDATE ".IMAGES_TABLE." SET ".implode(", ", $cond)." WHERE id='".$v['id']."'");
				}
			}
		}
	}


	/**
	 * Delete image
	 *
	 * @param int|string $image       - id or attachedto
	 * @param boolean    $clwhere     - type $image param
	 * 				false for id or attachedto
	 * 				true for another condition
	 */
	public function remove_images($image, bool $clwhere=false) {

                global $db;

		if(is_numeric($image) || is_integer($image)) {
			$cond = " id='".$image."' ";
		}
		else {
			$cond = " attachedto='".$image."' ";
		}

		if($clwhere) {
			$cond = $image;
		}

                $q = $db->query("SELECT id, filename, fileext FROM ".IMAGES_TABLE." WHERE ".$cond);
                while($row = $db->fetch_assoc($q)) {
                	if(!empty($row)) {
                		$original = $row['filename']."_original.".$row['fileext'];
                		$resize = $row['filename']."_resize.".$row['fileext'];
                		$thumb = $row['filename']."_thumb.".$row['fileext'];

				# delete unique name
				//$this->erase_image(_UPLOADIMAGES."/".$row['filename'].".".$row['fileext']);

                		# delete original
				$this->erase_image(_UPLOADIMAGES."/".$original);

				# delete resize
				$this->erase_image(_UPLOADIMAGES."/".$resize);

				# delete thumb
				$this->erase_image(_UPLOADIMAGES."/".$thumb);
                	}
                }

                $db->query("DELETE FROM ".IMAGES_TABLE." WHERE ".$cond);
        }


	/**
	 * Erase image file
	 *
	 * @param $image
	 */
	public function erase_image($image) {

		global $logger;

		if(is_file($image)) {
			unlink($image);
			$logger->log("Изображение ".basename($image)." удалено");
		}
		else {
			$logger->log("Не удалось найти изображение ".basename($image), "error");
		}
	}


	/**
	 * This function checks the input parameters of width and height for generating small images.
	 */
	public function check_post_thumb_parametrs() {

		global $post;

		if(!isset($post->thumb_img_width)) {
			$post->thumb_img_width = 0;
		}
		if(!isset($post->thumb_img_height)) {
			$post->thumb_img_height = 0;
		}

		$post->thumb_img_width = round($post->thumb_img_width);
		$post->thumb_img_height = round($post->thumb_img_height);

		if($post->thumb_img_width < 16) {
			$post->thumb_img_width = 0;
		}
		if($post->thumb_img_height < 16) {
			$post->thumb_img_height = 0;
		}
	}


	/**
	 * This function create an array of valid image extensions allowed for upload to server.
	 *
	 * @return array
	 */
	public function get_allow_images() {
		$imagetype = [];
		require _LIB."/mimetype.php";

		$allow_exts = [];
		foreach($imagetype AS $itype) {
			$allow_exts[$itype['mime_type']] = $itype['ext'];
		}

		return $allow_exts;
	}
}
