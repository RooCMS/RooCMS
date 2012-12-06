<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS File Class
|	Author:	alex Roosso
|	Copyright: 2010-2011 (c) RooCMS. 
|	Web: http://www.roocms.com
|	All rights reserved.
|----------------------------------------------------------
|	This program is free software; you can redistribute it and/or modify
|	it under the terms of the GNU General Public License as published by
|	the Free Software Foundation; either version 2 of the License, or
|	(at your option) any later version.
|	
|	Данное программное обеспечение является свободным и распространяется
|	по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
|	При любом использовании данного ПО вы должны соблюдать все условия
|	лицензии.
|----------------------------------------------------------
|	Build: 			23:08 13.11.2010
|	Last Build: 	1:12 17.10.2011
|	Version file:	1.00 build 4
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$files = new Files;

class Files {

	// Функция проверки Content-type 
	// Надо написать!
	public function mimetype($file) {
		
		if(file_exists($file))	
			$fileinfo = apache_lookup_uri($file);
		
		if(isset($fileinfo->content_type))	
			debug($fileinfo);
	}
	
	
	// Check file size ========================================
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
	
	
	// Создание имени файлов
	public function create_filename($ext, $prefix="") {
	
		$r = RndCode(7, "RooCMSversion07");
		$filename = $prefix."_".time()."_".$r.".".$ext;

		return $filename;
	}
	
	
	// Смотрим оригинальное расширение имени файла.
	public function check_ext($filename) {
		
		$files = explode(".",mb_strtolower($filename, 'utf8'));
		$c = count($files) - 1;
		$ext = $files[$c];
		
		return $ext;
	}
	
	
	// Функция загрузки файлов
	public function upload($file, $prefix="", $types="all", $path=_UPLOADFILES) {
	
		global $config, $parse, $filetype;

		
		$filename = false;
	
		// Если $_FILES не является массивом
		if(!is_array($_FILES[$file]['tmp_name'])) {
			if(isset($_FILES[$file]['tmp_name']) && $_FILES[$file]['error'] == 0) {
				
				// Смотрим оригинальное расширение файла
				$fileext = $this->check_ext($_FILES[$file]['name']);
				
				
				// Грузим апельсины бочками
				for($i=0;$i<=count($filetype)-1;$i++) {
					if($_FILES[$file]['type'] == $filetype[$i]['type'] && $fileext == $filetype[$i]['ext']) {
					
						// Создаем имя файлу.
						$filename['name'] = $this->create_filename($fileext, $prefix);
						$filename['ext'] = $fileext;
						$filename['real_name'] = $parse->escape_string(str_ireplace(".".$fileext, "", $_FILES[$file]['name']));
						
						// Копируем файл
						copy($_FILES[$file]['tmp_name'], $path."/".$filename['name']);
						
						// Если загрузка прошла и файл на месте
						if(!file_exists($path."/".$filename['name'])) $filename = false;
					}
				}
			}
			else {
				// вписать сообщение об ошибке.
				// впрочем ещё надо и обработчик ошибок написать.
				$filename = false;
			}
			
			return $filename;
		}
		// Если $_FILES является массивом
		else {
			foreach($_FILES[$file]['tmp_name'] AS $key=>$value) {
				
				// Сброс на случай отказа по разрешениям типа, только одного из элементов массива.
				$filename = false;
				
				if(isset($_FILES[$file]['tmp_name'][$key]) && $_FILES[$file]['error'][$key] == 0) {
					
					
					// Смотрим оригинальное расширение файла
					$fileext = $this->check_ext($_FILES[$file]['name'][$key]);

					
					// Грузим апельсины бочками
					for($i=0;$i<=count($filetype)-1;$i++) {
						if($_FILES[$file]['type'][$key] == $filetype[$i]['type'] && $fileext == $filetype[$i]['ext']) {
						
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
					// вписать сообщение об ошибке.
					// впрочем ещё надо и обработчик ошибок написать.
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