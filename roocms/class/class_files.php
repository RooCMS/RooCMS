<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	File Class
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*	RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
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
*   along with this program.  If not, see <http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
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

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//	files class

$files = new Files;

class Files {

	/**
	* Функция проверки Content-type
	* [не дописана]
	*
	* @param string $file - файл для проверки
	*/
	public function mimetype($file) {

		if(file_exists($file))
			$fileinfo = apache_lookup_uri($file);

		if(isset($fileinfo->content_type))
			debug($fileinfo);
	}


	/**
	* Проверка размера файла
	*
	* @param string $file - Указывается путь к файлу и имя файла
	* @return string - Функция возвращает результат вида 10.2Kb или 2.10Mb
	*/
	public function file_size($file) {

		if(file_exists($file)) {
			$t = "Kb";

			$f = filesize($file);
			$f = $f / 1024;
			if($f > 1024) {
				$t = "Mb";
				$f = $f / 1024;
			}

			$f = round($f,2).$t;
		}
		else $f = false;

		return $f;
	}


	/**
	* Создание имени файлов
	*
	* @param string $ext       - Расширение файла
	* @param string $prefix    - Префикс имени файла
	* @return Имя файла
	*/
	public function create_filename($ext, $prefix="") {

    	# Переписать, добавив транслитерацию, а префиксы в конец имени переместить.

		if(trim($prefix) != "") $prefix = $prefix."_";
		$r = randcode(7, "RooCMSv1nb10");
		$filename = $prefix.time()."_".$r.".".$ext;

		return $filename;
	}


	/**
	* Узнаем расширение файла по его имени
	*
	* @param string $filename - Полное имя файла, включая расширение
	* @return string расширение файла без точки
	*/
	public function check_ext($filename) {

    	# Переписать с pathinfo();

		$files = explode(".",mb_strtolower($filename));
		$c = count($files) - 1;
		$ext = $files[$c];

		return $ext;
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
	* @param string $file - Параметр файла массива $_FILES
	* @param string $prefix - Префикс имени файла
	* @param array|string $types - Допустимые типы файлов (в будущем)
	* @param string $path - путь для загрузки файлов
	*/
	public function upload($file, $prefix="", $types="all", $path=_UPLOADFILES) {

    	# Переписать функцию!!!
    	# *** Больше проверок от "умников"

		global $config, $parse;


		# check allow type
		require_once _LIB."/mimetype.php";

		static $allow_exts = array();

		if(empty($allow_exts)) {
	        foreach($filetype AS $itype) {
        		$allow_exts[$itype['type']] = $itype['ext'];
			}
		}


		$filename = false;

		# Если $_FILES не является массивом
		if(!is_array($_FILES[$file]['tmp_name'])) {
			if(isset($_FILES[$file]['tmp_name']) && $_FILES[$file]['error'] == 0) {

				# Смотрим оригинальное расширение файла
				$fileext = $this->check_ext($_FILES[$file]['name']);


				# Грузим апельсины бочками
				if(array_key_exists($_FILES[$file]['type'], $allow_exts)) {
					# Создаем имя файлу.
					$filename['name'] = $this->create_filename($fileext, $prefix);
					$filename['ext'] = $fileext;
					$filename['real_name'] = $parse->escape_string(str_ireplace(".".$fileext, "", $_FILES[$file]['name']));

					# Копируем файл
					copy($_FILES[$file]['tmp_name'], $path."/".$filename['name']);

					# Если загрузка прошла и файл на месте
					if(!file_exists($path."/".$filename['name'])) $filename = false;
				}
			}
			else {
				# вписать сообщение об ошибке.
				# впрочем ещё надо и обработчик ошибок написать.
				$filename = false;
			}

			return $filename;
		}
		# Если $_FILES является массивом
		else {
			foreach($_FILES[$file]['tmp_name'] AS $key=>$value) {

				# Сброс на случай отказа по разрешениям типа, только одного из элементов массива.
				$filename = false;

				if(isset($_FILES[$file]['tmp_name'][$key]) && $_FILES[$file]['error'][$key] == 0) {

					# Смотрим оригинальное расширение файла
					$fileext = $this->check_ext($_FILES[$file]['name'][$key]);

					# Грузим апельсины бочками
					if(array_key_exists($_FILES[$file]['type'][$key], $allow_exts)) {
						// Создаем имя файлу.
						$filename['name'] = $this->create_filename($fileext, $prefix);
						$filename['ext'] = $fileext;
						$filename['real_name'] = $parse->escape_string(str_ireplace(".".$fileext, "", $_FILES[$file]['name'][$key]));

						// Копируем файл
						copy($_FILES[$file]['tmp_name'][$key], $path."/".$filename['name']);

						// Если загрузка прошла и файл на месте
						if(!file_exists($path."/".$filename['name'])) $filename = false;
					}
				}
				else {
					# вписать сообщение об ошибке.
					# впрочем ещё надо и обработчик ошибок написать.
					$filename = false;
				}

				$names[] = $filename;
			}

			if(isset($names) && count($names) > 0) return $names;
			else return false;
		}
	}
}

?>