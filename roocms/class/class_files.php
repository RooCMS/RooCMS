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
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class Files
 */
class Files {

	/**
	 * Функция проверки Content-type
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
	 * Создание имени файлов
	 *
	 * @param string $filename - Имя файла
	 * @param string $prefix   - Префикс имени файла
	 * @param string $pofix    - Пофикс имени файла
	 *
	 * @param string $path     - путь к папке для загрузки изображений.
	 *
	 * @return string Имя файла
	 */
	public function create_filename($filename, $prefix="", $pofix="", $path=_UPLOADIMAGES) {

		global $parse;

		# убиваем прицепившиейся расширение к имени файла
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

		# Чистим имя файла от "левых" символов.
		$filename = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array(' ','-','_',''), $filename);

		# check filename leight
		$length = mb_strlen($ext) + 3;
		$length += mb_strlen($prefix) + 1;
		$length += mb_strlen($pofix) + 1;

		$filelength = mb_strlen($filename);

		# постараемся не выскочить за длину допустимого пути.
		$maxlenght = PHP_MAXPATHLEN - mb_strlen(__DIR__);
		if($length + $filelength > $maxlenght) {
			$maxfilelength = $maxlenght - $length;
                        $filename = mb_substr($filename,0,$maxfilelength);
		}

		$filename = $this->check_uniq_filename($prefix.$filename.$pofix, $ext, $path);

		return $filename;
	}


	/**
	 * Выгружаем присоедененные файлы
	 *
	 * @param string $cond  параметр указывающий на элемент к которому прикреплены изображения
	 * @param int    $from  стартовая позиция для загрузки файлов
	 * @param int    $limit лимит загружаемых файлов
	 *
	 * @return array $data  массив с данными о файлах.
	 */
	public function load_files($cond, $from = 0, $limit = 0) {

		global $db;

		$data = [];

		$l = ($limit != 0) ? "LIMIT {$from},{$limit}" : "" ;

		$q = $db->query("SELECT id, filename, fileext, filetitle, sort FROM ".FILES_TABLE." WHERE attachedto='{$cond}' ORDER BY sort ASC ".$l);
		while($file = $db->fetch_assoc($q)) {
			$file['file']	= $file['filename'].".".$file['fileext'];
			$data[] = $file;
		}

		return $data;
	}


	/**
	 * Функция загрузки файлов
	 *
	 * @param string       $file        Параметр файла массива $_FILES
	 * @param string       $attached    Элемент родитель файла
	 * @param string       $prefix      Префикс имени файла
	 * @param string|array $allowtypes  Допустимые типы файлов (в будущем)
	 * @param string       $path        путь для загрузки файлов
	 *
	 * @return array|false
	 */
	public function upload($file, $attached, $prefix="", $allowtypes="", $path=_UPLOADFILES) {

		# Объявляем выходной массив
		$files = [];

		# Составляем массив для проверки разрешенных типов файлов к загрузке
		$allow_exts = $this->get_allow_exts($allowtypes);

		# Если $_FILES не является массивом конвертнем в массив
		# Я кстати в курсе, что сам по себе $_FILES уже массив. Тут в другом смысл.
		$upfiles = [];
		if(!is_array($_FILES[$file]['tmp_name'])) {
			foreach($_FILES[$file] AS $k=>$v) {
				$upfiles[$file][$k][$file] = $v;
			}
		}
		else {
			$upfiles[$file] = $_FILES[$file];
		}


		# приступаем к обработке
		foreach($upfiles[$file]['tmp_name'] AS $key=>$value) {
			if(isset($upfiles[$file]['tmp_name'][$key]) && $upfiles[$file]['error'][$key] == 0) {

				$upload = false;

				# ext
				$ffn = explode(".", $upfiles[$file]['name'][$key]);
				$ext = array_pop($ffn);

				# исключение для tar.gz (в будущем оформим нормальным образом)
				if($upfiles[$file]['ext'][$key] == "gz") {
					$upfiles[$file]['ext'][$key] = "tar.gz";
				}

				# Грузим апельсины бочками
				if(array_key_exists($ext, $allow_exts)) {

					# create filename
					$filename  = $this->create_filename($upfiles[$file]['name'][$key], $prefix);
					$ext       = $allow_exts[$ext];

					# Создаем титул файлу
					$filetitle = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9а-яА-Я\-\._]+/msi)'), array(' ','-','_',''), $upfiles[$file]['name'][$key]);


					# Сохраняем оригинал
					copy($upfiles[$file]['tmp_name'][$key], $path."/".$filename.".".$ext);

					# Если загрузка прошла и файл на месте
					$upload = is_file($path."/".$filename.".".$ext);
				}

				# if upload true
				if(!$upload) {
					$filename = false;
				}
			}
			else {
				# вписать сообщение об ошибке.
				# впрочем ещё надо и обработчик ошибок написать.
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
	 * Загружаем информацию о файлах в БД
	 *
	 * @param string $filename имя файла без $pofix
	 * @param string $filetitle название файла
	 * @param mixed  $attached родитель файла
	 */
	public function insert_file($filename, $filetitle, $attached) {

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
	 * @param int|string $file - file identificator
	 */
	public function remove_files($file) {

		global $db;

		if(is_numeric($file) || is_integer($file)) {
			$cond = " id='".$file."' ";
		}
		else {
			$cond = " attachedto='".$file."' ";
		}

		$q = $db->query("SELECT id, filename, fileext FROM ".FILES_TABLE." WHERE ".$cond);
		while($row = $db->fetch_assoc($q)) {
			if(!empty($row)) {
				$filename = $row['filename'].".".$row['fileext'];

				# delete
				$this->erase_file(_UPLOADFILES."/".$filename);
			}
		}

		$db->query("DELETE FROM ".FILES_TABLE." WHERE ".$cond);
	}


	/**
	 * Функция составляет массив допустимых расширений файлов разрешенных для загрузки на сервер.
	 *
	 * @param string|array $allowtypes     Допустимые типы файлов (в будущем)
	 *
	 * @return mixed Возвращает массив с допустимыми расширениями изображения для загрузки на сервер
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
	public function file_size($file) {

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
	 * Узнаем расширение файла по его имени
	 *
	 * @param string $filename Полное имя файла, включая расширение
	 *
	 * @return string расширение файла без точки
	 */
	public function get_ext($filename) {

		$pi = pathinfo($filename);
		$ext = $pi['extension'];

		return $ext;
	}


	/**
	 * Show file perms
	 *
	 * @param string $file - full path to file
	 *
	 * @return int|string
	 */
	public function get_fileperms($file) {
		return mb_substr(sprintf('%o', fileperms($file)), -4);
	}


	/**
	 * Write file on disk
	 *
	 * @param string $file    - full path to file
	 * @param string $context - data for write in file
	 */
	public function write_file($file, $context) {
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
	public function erase_file($file) {

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
	private function check_uniq_filename($filename, $ext, $path=_UPLOADIMAGES) {

		if(is_file($path."/".$filename.".".$ext) || is_file($path."/".$filename."_resize.".$ext)) {
			$filename .= "_".randcode(3,"RooCMS-BestChoiceForYourSite");
			$filename = $this->check_uniq_filename($filename, $ext, $path);
		}

		return $filename;
	}
}
