<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	File Class
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.9
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
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


	//#####################################################
	// Функция загрузки файлов
	public function upload($file, $prefix="", $types="all", $path=_UPLOADFILES) {

		global $config, $parse;

		require_once _LIB."/mimetype.php";

		$filename = false;

		# Если $_FILES не является массивом
		if(!is_array($_FILES[$file]['tmp_name'])) {
			if(isset($_FILES[$file]['tmp_name']) && $_FILES[$file]['error'] == 0) {

				# Смотрим оригинальное расширение файла
				$fileext = $this->check_ext($_FILES[$file]['name']);


				# Грузим апельсины бочками
				foreach($filetype AS $ftype) {
					if($_FILES[$file]['type'] == $ftype['type'] && $fileext == $ftype['ext']) {

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
					foreach($filetype AS $ftype) {
						if($_FILES[$file]['type'][$key] == $ftype['type'] && $fileext == $ftype['ext']) {

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