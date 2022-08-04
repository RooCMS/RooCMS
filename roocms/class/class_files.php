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
 * Class Files
 */
class Files {

	/**
	 * Check Content-type
	 * [не дописана]
	 *
	 * @param string $file файл для проверки
	 *
	 * @return object
	 */
	/*public function mimetype($file) {

		global $debug;

		if(is_file($file) && array_search("apache2handler", $debug->phpextensions)) {
			$fileinfo = apache_lookup_uri($file);
		}

		if(isset($fileinfo->content_type)) {
			debug($fileinfo);
		}
	}*/


	/**
	 * Create file name
	 *
	 * @param string $filename - file name
	 * @param string $prefix   - prefix file name
	 * @param string $pofix    - poffix file name
	 *
	 * @param string $path     - path to upload (default images, use conts _UPLOADFILES for other files)
	 *
	 * @return string - new file name
	 */
	public function create_filename(string $filename, string $prefix="", string $pofix="", string $path=_UPLOADIMAGES) {

		global $parse;

		# get filename without extension
		$ext = $this->get_ext($filename);
		$filename = str_ireplace(".".$ext, "", $filename);

		# prefix
		if(trim($prefix) != "")	{
			$prefix .= "_";
		}
		# pofix
		if(trim($pofix)  != "")	{
			$pofix = "_".$pofix;
		}

		# transliterate
		$filename = $parse->text->transliterate($filename, "lower");

		# Clear file name from extraneous characters
		$filename = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array(' ','-','_',''), $filename);

		# check filename leight
		$length = mb_strlen($ext) + 3;
		$length += mb_strlen($prefix) + 1;
		$length += mb_strlen($pofix) + 1;

		$filelength = mb_strlen($filename);

		# check leight filepath
		$maxlenght = PHP_MAXPATHLEN - mb_strlen(__DIR__);
		if($length + $filelength > $maxlenght) {
			$maxfilelength = $maxlenght - $length;
                        $filename = mb_substr($filename,0,$maxfilelength);
		}

		return $this->check_uniq_filename($prefix.$filename.$pofix, $ext, $path);
	}


	/**
	 * Load atteched files
	 *
	 * @param string $cond  - condition attached
	 * @param int    $from  - start position for load
	 * @param int    $limit - limit
	 *
	 * @return array $data  files info
	 */
	public function load_files(string $cond, int $from = 0, int $limit = 0) {

		global $db;

		$data = [];

		$l = ($limit != 0) ? "LIMIT {$from},{$limit}" : "" ;

		$q = $db->query("SELECT id, filename, fileext, filetitle, sort FROM ".FILES_TABLE." WHERE attachedto='{$cond}' ORDER BY sort ".$l);
		while($file = $db->fetch_assoc($q)) {
			$file['file']	= $file['filename'].".".$file['fileext'];
			$data[] = $file;
		}

		return $data;
	}


	/**
	 * Upload Files
	 *
	 * @param string       $file       name array $_FILES
	 * @param string       $attached
	 * @param string       $prefix     prefix file name
	 * @param string|array $allowtypes allowed file types (in future)
	 * @param string       $path       path to upload
	 *
	 * @return array|false
	 */
	public function upload(string $file, string $attached, string $prefix="", $allowtypes="", string $path=_UPLOADFILES) {

		# create output array data
		$files = [];

		# Construct array allowed extension
		$allow_exts = $this->get_allow_exts($allowtypes);

		# Construct array up files
		$upfiles = [];
		if(!is_array($_FILES[$file]['tmp_name'])) {
			foreach($_FILES[$file] AS $k=>$v) {
				$upfiles[$file][$k][$file] = $v;
			}
		}
		else {
			$upfiles[$file] = $_FILES[$file];
		}


		# processing
		foreach($upfiles[$file]['tmp_name'] AS $key=>$value) {
			if(isset($upfiles[$file]['tmp_name'][$key]) && $upfiles[$file]['error'][$key] == 0) {

				$upload = false;

				# ext
				$ffn = explode(".", $upfiles[$file]['name'][$key]);
				$ext = array_pop($ffn);

				# exception for tar.gz (TODO: !!!)
				if($ext == "gz") {
					$ext = "tar.gz";
				}

				# Let's dance
				if(array_key_exists($ext, $allow_exts)) {

					# create filename
					$filename  = $this->create_filename($upfiles[$file]['name'][$key], $prefix);
					$ext       = $allow_exts[$ext];

					# Create file title
					$filetitle = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9а-яА-Я\-\._]+/msi)'), array(' ','-','_',''), $upfiles[$file]['name'][$key]);


					# save
					copy($upfiles[$file]['tmp_name'][$key], $path."/".$filename.".".$ext);

					# check file exists
					$upload = is_file($path."/".$filename.".".$ext);
				}

				# if upload true
				if(!$upload) {
					$filename = false;
				}
			}
			else {
				# TODO: вписать сообщение об ошибке.
				# TODO: впрочем ещё надо и обработчик ошибок написать.
				$filename = false;
			}

			if($filename !== false) {
				# upload
				$this->insert_file($filename.".".$ext, $filetitle, $attached);

				# callback array
				$files[] = $filename.".".$ext;
			}
		}

		# return filename array for insert to db
		return (count($files) > 0) ? $files : false ;
	}


	/**
	 * Upload file info to db
	 *
	 * @param string $filename  - file name withput $pofix
	 * @param string $filetitle - file title
	 * @param mixed  $attached  - site item for attached file
	 */
	public function insert_file(string $filename, string $filetitle, $attached) {

		global $db, $logger;

		$fileinfo = pathinfo($filename);

		$db->query("INSERT INTO ".FILES_TABLE." (attachedto, filename, fileext, filetitle)
						VALUES ('".$attached."', '".$fileinfo['filename']."', '".$fileinfo['extension']."', '".$filetitle."')");

		# msg
		$logger->log("Файл ".basename($filename)." успешно загружен на сервер");
	}


	/**
	 * Remove files from data base
	 *
	 * @param int|string $file        - file identificator
	 * @param boolean    $clwhere     - type $file param
	 * 				false for id or attachedto
	 * 				true for another condition
	 */
	public function remove_files($file, bool $clwhere=false) {

		global $db;

		if(is_numeric($file) || is_integer($file)) {
			$cond = " id='".$file."' ";
		}
		else {
			$cond = " attachedto='".$file."' ";
		}

		if($clwhere) {
			$cond = $file;
		}

		$q = $db->query("SELECT id, filename, fileext FROM ".FILES_TABLE." WHERE ".$cond);
		while($row = $db->fetch_assoc($q)) {
			if(!empty($row)) {
				$filename = $row['filename'].".".$row['fileext'];

				# delete file from disk
				$this->erase_file(_UPLOADFILES."/".$filename);
			}
		}

		# delete file from db
		$db->query("DELETE FROM ".FILES_TABLE." WHERE ".$cond);
	}


	/**
	 * Construct array allowed file types
	 *
	 * @param string|array $allowtypes     allowed file types (in future)
	 *
	 * @return array
	 */
	public function get_allow_exts($allowtypes="") {
		$filetype = [];
		require _LIB."/mimetype.php";

		$allow_exts = [];

		# listing allow types
		if($allowtypes != "") {

			if(!is_array($allowtypes)) {
				$exts = preg_split("/[\s,-]+/", $allowtypes);
				$allowtypes &= $exts;
			}

			# create callback array
			foreach($filetype AS $itype) {
				if(in_array($itype['ext'], $allowtypes)) $allow_exts[$itype['ext']] = $itype['ext'];
			}
		}
		else {
			# create callback array
			foreach($filetype AS $itype) {
				$allow_exts[$itype['ext']] = $itype['ext'];
			}
		}

		return $allow_exts;
	}


	/**
	 * Get file size
	 *
	 * @param string $file - path to file and file name
	 *
	 * @return string|false - return data file size. Example: 10.2Kb or 1.21 Mb
	 */
	public function file_size(string $file) {

		if(is_file($file)) {
			$t = "Kb";

			$f = filesize($file) / 1024;
			if($f > 1024) {
				$t = "Mb";
				$f = $f / 1024;
			}

			$f = round($f,2).$t;
		}
		else {
			$f = false;
		}

		return $f;
	}


	/**
	 * Get file extension
	 *
	 * @param string $filename - full file name with extension
	 *
	 * @return string - file extension without dot
	 */
	public function get_ext(string $filename) {

		$pi = pathinfo($filename);

		return $pi['extension'];
	}


	/**
	 * Show file perms
	 *
	 * @param string $file - full path to file
	 *
	 * @return int|string
	 */
	public function get_fileperms(string $file) {
		return mb_substr(sprintf('%o', fileperms($file)), -4);
	}


	/**
	 * Write file on disk
	 *
	 * @param string $file    - full path to file
	 * @param string $context - data for write in file
	 */
	public function write_file(string $file, string $context) {
		$f = fopen($file, "w+");
		if(is_writable($file) && is_resource($f)) {
			fwrite($f, $context);
		}
		fclose($f);
	}


	/**
	 * Erase file from disk
	 *
	 * @param string $file - full path to file
	 */
	public function erase_file(string $file) {

		global $logger;

		if(is_file($file)) {
			unlink($file);
			$logger->log("Удален файл: ".basename($file));
		}
		elseif(!is_file($file)) {
			$logger->error("Не удалось найти файл ".basename($file), "error");
		}
	}


	/**
	 * Check filename for avoid duplication and mashing
	 *
	 * @param string $filename - filename for check
	 * @param string $ext      - file extension
	 * @param string $path     - path to file folder
	 *
	 * @return string - new filename
	 */
	private function check_uniq_filename(string $filename, string $ext, string $path=_UPLOADIMAGES) {

		if(is_file($path."/".$filename.".".$ext) || is_file($path."/".$filename."_resize.".$ext)) {
			$filename .= "_".randcode(3,"RooCMS-BestChoiceForYourSite");
			$filename = $this->check_uniq_filename($filename, $ext, $path);
		}

		return $filename;
	}
}
