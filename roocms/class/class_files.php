<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
 *
 *   Это программа является свободным программным обеспечением. Вы можете
 *   распространять и/или модифицировать её согласно условиям Стандартной
 *   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 *   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 *   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 *   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 *   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 *   Общественную Лицензию GNU для получения дополнительной информации.
 *
 *   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 *   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

/**
 * @package      RooCMS
 * @subpackage	 Engine RooCMS classes
 * @author       alex Roosso
 * @copyright    2010-2018 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.3.2
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
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
	 * @param string $file - файл для проверки
	 *
	 * @return object
	 */
	/*public function mimetype($file) {

		global $debug;

		if(file_exists($file) && array_search("apache2handler", $debug->phpextensions)) {
			$fileinfo = apache_lookup_uri($file);
		}

		if(isset($fileinfo->content_type)) {
			debug($fileinfo);
		}
	}*/


	/**
	* Проверка размера файла
	*
	* @param string $file - Указывается путь к файлу и имя файла
	* @return string - Функция возвращает результат вида 10.2Kb или 2.10Mb
	*/
	public function file_size($file) {

		if(file_exists($file)) {
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
	 * Создание имени файлов
	 *
	 * @param string $filename - Имя файла
	 * @param string $prefix   - Префикс имени файла
	 * @param string $pofix    - Пофикс имени файла
	 *
	 * @return mixed|string Имя файла
	 */
	public function create_filename($filename, $prefix="", $pofix="") {

		global $parse;
		static $names = array();

		# убиваем прицепившиейся расширение к имени файла
		$pi = pathinfo($filename);
		$filename = str_ireplace(".".$pi['extension'], "", $filename);

		# префикс
		if(trim($prefix) != "")	{
			$prefix .= "_";
		}
		# постфикс
		if(trim($pofix)  != "")	{
			$pofix = "_".$pofix;
		}

		# транслит
		$filename = $parse->text->transliterate($filename, "lower");

		# Чистим имя файла от "левых" символов.
		$filename = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array(' ','-','_',''), $filename);

		# Проверяем длину имения файла
		$length = 5;
		if($prefix != "") {
			$length += mb_strlen($prefix) + 1;
		}
		if($pofix != "") {
			$length += mb_strlen($pofix) + 1;
		}
		$length += mb_strlen(time());

		$filelength = mb_strlen($filename);

		# постараемся не выскочить за длину допустимого пути.
		$maxlenght = PHP_MAXPATHLEN - __DIR__;
		if($length + $filelength > $maxlenght) {
			$maxfilelength = $maxlenght - $length;
                        $filename = mb_substr($filename,0,$maxfilelength);
		}

		# suffix for unduplicated
		$suffix = (in_array($filename,$names)) ? "_".randcode(3,"RooCMSbestChoiceForYourSite"): "" ;
		$names[] = $filename;

		$filename = $prefix.$filename.$suffix."_".time().$pofix;

		return $filename;
	}


	/**
	* Узнаем расширение файла по его имени
	*
	* @param string $filename - Полное имя файла, включая расширение
	* @return string расширение файла без точки
	*/
	public function get_ext($filename) {

		$pi = pathinfo($filename);
		$ext = $pi['extension'];

		return $ext;
	}


	/**
	 * Выгружаем присоедененные файлы
	 *
	 * @param string $where - параметр указывающий на элемент к которому прикреплены изображения
	 * @param int    $from - стартовая позиция для загрузки файлов
	 * @param int    $limit - лимит загружаемых файлов
	 *
	 * @return array $data - массив с данными о файлах.
	 */
	public function load_files($where, $from = 0, $limit = 0) {

		global $db;

		$data = array();

		$l = ($limit != 0) ? "LIMIT {$from},{$limit}" : "" ;

		$q = $db->query("SELECT id, filename, fileext, sort FROM ".FILES_TABLE." WHERE attachedto='{$where}' ORDER BY sort ASC ".$l);
		while($file = $db->fetch_assoc($q)) {
			$file['file']	= $file['filename'].".".$file['fileext'];
			$data[] = $file;
		}

		return $data;
	}


	/**
	 * Отображение прав доступа в виде восьмеричного числа
	 *
	 * @param string $file  - название файла с указанием полного пути до него
	 * @return int
	 */
	public function show_fileperms($file) {
		return mb_substr(sprintf('%o', fileperms($file)), -4);
	}


	/**
	 * Функция загрузки файлов
	 *
	 * @param string	$file   - Параметр файла массива $_FILES
	 * @param string	$prefix - Префикс имени файла
	 * @param array|string	$types  - Допустимые типы файлов (в будущем)
	 * @param string	$path	- путь для загрузки файлов
	 *
	 * @return array|bool
	 */
	public function upload($file, $prefix="", $types="all", $path=_UPLOADFILES) {

    	        # Переписать функцию!!!
    	        # *** Больше проверок от "умников"

		# Объявляем выходной массив
		$files = array();

		# Составляем массив для проверки разрешенных типов файлов к загрузке
		static $allow_exts = array();
		if(empty($allow_exts)) {
			$allow_exts = $this->get_allow_exts();
		}


		# Если $_FILES не является массивом конвертнем в массив
		# Я кстати в курсе, что сам по себе $_FILES уже массив. Тут в другом смысл.
		if(!is_array($_FILES[$file]['tmp_name'])) {
			foreach($_FILES[$file] AS $k=>$v) {
				$_FILES[$file][$k][$file] = $v;
			}
		}


		# приступаем к обработке
		foreach($_FILES[$file]['tmp_name'] AS $key=>$value) {
			if(isset($_FILES[$file]['tmp_name'][$key]) && $_FILES[$file]['error'][$key] == 0) {

				$upload = false;

				# ext
				$ffn = explode(".", $_FILES[$file]['name'][$key]);
				$_FILES[$file]['ext'][$key] = array_pop($ffn);

				# исключение для tar.gz (в будущем оформим нормальным образом)
				if($_FILES[$file]['ext'][$key] == "gz") {
					$_FILES[$file]['ext'][$key] = "tar.gz";
				}

				# Грузим апельсины бочками
				if(array_key_exists($_FILES[$file]['ext'][$key], $allow_exts)) {

					# Создаем имя файлу.
					$ext = $allow_exts[$_FILES[$file]['ext'][$key]];
					$filename = $this->create_filename($_FILES[$file]['name'][$key], $prefix);

					# Сохраняем оригинал
					copy($_FILES[$file]['tmp_name'][$key], $path."/".$filename.".".$ext);

					# Если загрузка прошла и файл на месте
					$upload = (!file_exists($path."/".$filename.".".$ext)) ? false : true ;
				}

				# Если не загрузка удалась
				if(!$upload) {
					$filename = false;
				}
			}
			else {
				# вписать сообщение об ошибке.
				# впрочем ещё надо и обработчик ошибок написать.
				$filename = false;
			}

			if($filename) {
				$files[] = $filename.".".$ext;
			}
		}

		# Возвращаем массив имен файлов для внесения в БД
		return (count($files) > 0) ? $files : false ;
	}


	/**
	 * Функция составляет массив допустимых расширений файлов разрешенных для загрузки на сервер.
	 *
	 * @return mixed Возвращает массив с допустимыми расширениями изображения для загрузки на сервер
	 */
	public function get_allow_exts() {
		require _LIB."/mimetype.php";

		foreach($filetype AS $itype) {
			$allow_exts[$itype['ext']] = $itype['ext'];
		}

		return $allow_exts;
	}


	/**
	 * Загружаем информацию о файлах в БД
	 *
	 * @param string $filename - имя файла без $pofix
	 * @param mixed  $attached - родитель файла
	 */
	public function insert_file($filename, $attached) {

		global $db, $logger;

		$fileinfo = pathinfo($filename);

		$db->query("INSERT INTO ".FILES_TABLE." (attachedto, filename, fileext)
						VALUES ('".$attached."', '".$fileinfo['filename']."', '".$fileinfo['extension']."')");

		# msg
		$logger->log("Файл ".$filename." успешно загружен на сервер");
	}


	/**
	 * Функция удаления файлов
	 *
	 * @param int|string $file - указать числовой идентификатор или attachedto
	 * @param boolean    $clwhere - флаг указывает как считывать параметр $file
	 * 				положение false указывает, что передается параметр id или attachedto
	 * 				положение true указывает, что передается полностью выраженное условие
	 */
	public function delete_files($file, $clwhere=false) {

		global $db, $logger;

		if(is_numeric($file) || is_integer($file)) {
			$where = " id='".$file."' ";
		}
		else {
			$where = " attachedto='".$file."' ";
		}

		if($clwhere) {
			$where = $file;
		}

		$q = $db->query("SELECT id, filename, fileext FROM ".FILES_TABLE." WHERE ".$where);
		while($row = $db->fetch_assoc($q)) {
			if(!empty($row)) {
				$filename = $row['filename'].".".$row['fileext'];

				# delete
				if(file_exists(_UPLOADFILES."/".$filename)) {
					unlink(_UPLOADFILES."/".$filename);
					$logger->log("Файл ".$filename." удален");
				}
				elseif(!file_exists(_UPLOADFILES."/".$filename)) {
					$logger->error("Не удалось найти файл ".$filename, "error");
				}
			}
		}

		$db->query("DELETE FROM ".FILES_TABLE." WHERE ".$where);
	}
}

?>