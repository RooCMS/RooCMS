<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS GD Class
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
|	Build: 			18:43 30.11.2010
|	Last Build: 	9:20 15.10.2011
|	Version file:	1.00 build 7
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$gd = new GD;

class GD {

	# GD info
	var $info		= array();
	
	# text copyright (default = $var['title'] )
	var $copyright	= "";
	
	# domain addres
	var $domain		= "";
	
	# thumbnail size
	var $tsize		= array('w' => 100,
							'h' => 100);
	
	
	// Start ==============================================
	function __construct() {
		
		global $config, $var;
	
		// Получить GD info
		$this->info = gd_info();
		
		if($config->gd_resize_image) {
			$this->tsize['w'] = $config->gd_thumb_image_width;
			$this->tsize['h'] = $config->gd_thumb_image_height;
		}
		
		// Если используем watermark
		if($config->gd_use_watermark) {
			// watermark string one
			if(trim($config->gd_watermark_string_one) != "") {
				$this->copyright = $config->gd_watermark_string_one;
			}
			else {
				$this->copyright =& $var['title'];
			}

			// watermark string two
			if(trim($config->gd_watermark_string_two) != "") {
				$this->domain = $config->gd_watermark_string_two;
			}
			else {
				$this->domain = $_SERVER['SERVER_NAME'];
			}
		}
	}
	
	
	// Загрузка картинок через $_POST =====================
	public function post_upload($file, $prefix="", $resize=true, $watermark=true, $path=_UPLOAD, $check_empty=true) {
		
		global $config, $imagetype, $files;
		
		// Если $_FILES не является массивом
		if(!is_array($_FILES[$file]['tmp_name'])) {
			if(isset($_FILES[$file]['tmp_name']) && $_FILES[$file]['error'] == 0) {
				
				$upload = false;
				
				// Грузим апельсины бочками
				for($i=0;$i<=count($imagetype)-1;$i++) {
					if($_FILES[$file]['type'] == $imagetype[$i]['type']) {
					
						// Создаем имя файлу.
						$ext = $imagetype[$i]['ext'];
						$filename = $files->create_filename($ext, $prefix);
						
						// Копируем файл
						copy($_FILES[$file]['tmp_name'], $path."/".$filename);
						
						// Если загрузка прошла и файл на месте
						$upload = true;
						if(!file_exists($path."/".$filename)) $upload = false;
					}
				}
				
				// Если загрузка удалась
				if($upload) {
					
					if($config->gd_resize_image && $resize) {
						// Создаем миниатюру
						$this->thumbnail($filename, $ext, $path);
					}
					
					if($config->gd_use_watermark && $watermark) {
						// наносим ватермарк
						$this->watermark($filename, $ext, $path);
					}
				}
				else {
					// Обработчик если загрузка не удалась =)
					$filename = false;
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
				if(isset($_FILES[$file]['tmp_name'][$key]) && $_FILES[$file]['error'][$key] == 0) {
					
					$upload = false;
					
					// Грузим апельсины бочками
					for($i=0;$i<=count($imagetype)-1;$i++) {
						if($_FILES[$file]['type'][$key] == $imagetype[$i]['type']) {
						
							// Создаем имя файлу.
							$ext = $imagetype[$i]['ext'];
							$filename = $files->create_filename($ext, $prefix);
							
							// Копируем файл
							copy($_FILES[$file]['tmp_name'][$key], $path."/".$filename);
							
							// Если загрузка прошла и файл на месте
							$upload = true;
							if(!file_exists($path."/".$filename)) $upload = false;
						}
					}
					
					// Если загрузка удалась
					if($upload) {
						
						if($config->gd_resize_image && $resize) {
							// Создаем миниатюру
							$this->thumbnail($filename, $ext, $path);
						}
						
						if($config->gd_use_watermark && $watermark) {
							// наносим ватермарк
							$this->watermark($filename, $ext, $path);
						}
					}
					else {
						// Обработчик если загрузка не удалась =)
						$filename = false;
					}
				}
				else {
					// вписать сообщение об ошибке.
					// впрочем ещё надо и обработчик ошибок написать.
					$filename = false;
				}
				
				if(!$check_empty) {
					$names[$key] = $filename;
				}
				else {
					if($filename) $names[] = $filename;
				}
			}
			
			if(isset($names) && count($names) > 0) return $names;
			else return false;
			
		}
	}
	
	
	// Функция генерация мини изображения
	public function thumbnail($filename, $ext, $path=_UPLOAD) {
	
		// определяем размер картинки
		$size = getimagesize($path."/".$filename);
		$w = $size[0];
		$h = $size[1];
		
		// вносим в память пустую превью и оригинальный файл, для дальнейшего издевательства над ними.
		$thumb 	= imagecreatetruecolor($this->tsize['w'], $this->tsize['h']);
		if($ext == "jpg")		$src 	= imagecreatefromjpeg($path."/".$filename);
		elseif($ext == "gif")	$src 	= imagecreatefromgif($path."/".$filename);
		elseif($ext == "png")	$src 	= imagecreatefrompng($path."/".$filename);
		
		// Проводим расчеты по сжатию превью и уменьшению в размерах ;)
		if($h > $w) {
			$n = ($h - $w) / 2;
			imagecopyresampled($thumb, $src, 0, 0, 0, $n, $this->tsize['w'], $this->tsize['h'], $w, $h-$n*2);
		}
		elseif($w > $h) {
			$n = ($w - $h) / 2;
			imagecopyresampled($thumb, $src, 0, 0, $n, 0, $this->tsize['w'], $this->tsize['h'], $w-$n*2, $h);
		}
		elseif($w == $h) {
			imagecopyresampled($thumb, $src, 0, 0, 0, 0, $this->tsize['w'], $this->tsize['h'], $w, $h);
		}
		
		// Создаем имя для превью
		$thumbname = str_replace(".".$ext, "", $filename);
		$thumbname = "thumb_".$thumbname.".".$ext;
		
		// льем превью 
		if($ext == "jpg")		imagejpeg($thumb,$path."/".$thumbname,90);
		elseif($ext == "gif")	imagegif($thumb,$path."/".$thumbname);
		elseif($ext == "png")	imagepng($thumb,$path."/".$thumbname);
		imagedestroy($thumb);
		imagedestroy($src);
	}
	
	
	// Генератор водяного знака ===========================
	public function watermark($filename, $ext, $path=_UPLOAD) {
		
		global $var;
		
		// определяем размер картинки
		$size = getimagesize($path."/".$filename);
		$w = $size[0];
		$h = $size[1];
		
		// вводим в память файл для издевательств
		if($ext == "jpg")		$src 	= imagecreatefromjpeg($path."/".$filename);
		elseif($ext == "gif")	$src 	= imagecreatefromgif($path."/".$filename);
		elseif($ext == "png")	$src 	= imagecreatefrompng($path."/".$filename);
		
		//удаляем оригинал
		unlink($path."/".$filename);
		
		
		// Gaussian blur matrix:
		// $matrix = array(
			// array( 1, 2, 2 ),
			// array( 2, 4, 2 ),
			// array( 1, 2, 1 )
		// );
		// imageconvolution($src, $matrix, 16, 0);

		
		// наклон
		$angle = 0;

		// Тень следом текст, далее цвет линии подложки
		$shadow 	= imagecolorallocatealpha($src, 0, 0, 0, 20);
		$color  	= imagecolorallocatealpha($src, 255, 255, 255, 20);
		$colorline  = imagecolorallocatealpha($src, 220, 220, 225, 66);
		
		// размер шрифта
		$size = 10;
		
		// выбираем шрифт
		$fontfile = ""._CMS."/fonts/trebuc.ttf";
		#$fontfile = ""._CMS."/fonts/verdana.ttf";
		
		//imagefilledrectangle($src, 0, $h-33, $w, $h-5, $colorline);
		//imagettfbbox($size, $angle, $fontfile, $this->domain);
		
		$this->copyright = $this->tounicode($this->copyright);
		imagettftext($src, $size, $angle, 7+1, $h-18+1, $shadow, $fontfile, $this->copyright);
		imagettftext($src, $size, $angle, 7-1, $h-18-1, $shadow, $fontfile, $this->copyright);
		imagettftext($src, $size, $angle, 7+1, $h-18-1, $shadow, $fontfile, $this->copyright);
		imagettftext($src, $size, $angle, 7-1, $h-18+1, $shadow, $fontfile, $this->copyright);
		imagettftext($src, $size, $angle, 7, $h-18, $color, $fontfile, $this->copyright);
		
		$this->domain = $this->tounicode($this->domain);
		imagettftext($src, $size, $angle, 7+1, $h-5+1, $shadow, $fontfile, $this->domain);
		imagettftext($src, $size, $angle, 7-1, $h-5-1, $shadow, $fontfile, $this->domain);
		imagettftext($src, $size, $angle, 7+1, $h-5-1, $shadow, $fontfile, $this->domain);
		imagettftext($src, $size, $angle, 7-1, $h-5+1, $shadow, $fontfile, $this->domain);
		imagettftext($src, $size, $angle, 7, $h-5, $color, $fontfile, $this->domain);
		
		
		
		//влимаем с ватермарком
		if($ext == "jpg")		imagejpeg($src,$path."/".$filename,90); 
		elseif($ext == "gif")	imagegif($src,$path."/".$filename); 
		elseif($ext == "png")	imagepng($src,$path."/".$filename); 
		imagedestroy($src);
	}
	
	
	// Преобразовываем текст из ISO8859-5 в Unicode =======
	// Использовать перед запуском imagettftext 
	protected function tounicode($text, $from="w") {
		$text = convert_cyr_string($text, $from, "i");
		$uni  = "";
		for($i=0, $len=mb_strlen($text, 'utf8'); $i<$len; $i++) {
			$char = $text{$i};
			$code = ord($char);
			$uni .= ($code>175)? "&#" . (1040+($code-176)) . ";" : $char;
		}
		
		return $uni;
	}
}

?>