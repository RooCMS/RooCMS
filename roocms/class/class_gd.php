<?php
/**
* @package	RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	GD Class
* @author	alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link		http://www.roocms.com
* @version	1.8.1
* @since	$date$
* @license	http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
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


// graphic class :: GD

class GD {

	# vars
	var $info	= array();				# Информация о GD расширении
	var $copyright	= "";					# Текст копирайта ( По умолчанию: $site['title'] )
	var $domain	= "";					# Адрес домена ( По умолчанию: $site['domain'] )
	var $msize	= array('w' => 900,'h' => 900);		# Максимальные размеры сохраняемого изображения
	var $tsize	= array('w' => 267,'h' => 150);		# Размеры миниатюры
	var $rs_quality	= 90;					# Качество обработанных изображений
	var $th_quality	= 90;					# Качество генерируемых миниматюр
	var $thumbtg	= "fill";				# Тип генерируемой миниатюры ( Возможные значения: fill - заливка, size - по размеру изображения )
	var $thumbbgcol	= array('r' => 0, 'g' => 0, 'b' => 0);	# Значение фонового цвета, если тип генерируемых миниатюр производится по размеру ( $thumbtg = size )



	/**
	* Let's go
	*
	*/
	function __construct() {

		global $config, $site, $parse;

		# Получить GD info
		$this->info = gd_info();

		# Устанавливаем размеры миниатюр из конфигурации
		if(isset($config->gd_thumb_image_width) && round($config->gd_thumb_image_width) >= 16)		$this->tsize['w'] = round($config->gd_thumb_image_width);
		if(isset($config->gd_thumb_image_height) && round($config->gd_thumb_image_height) >= 16)	$this->tsize['h'] = round($config->gd_thumb_image_height);


		# Устанавливаем максимальные размеры изображений
		if(isset($config->gd_image_maxwidth) && round($config->gd_image_maxwidth) >= 32 && round($config->gd_image_maxwidth) > $this->tsize['w'])	$this->msize['w'] = round($config->gd_image_maxwidth);
		if(isset($config->gd_image_maxheight) && round($config->gd_image_maxheight) >= 32 && round($config->gd_image_maxheight) > $this->tsize['h'])	$this->msize['h'] = round($config->gd_image_maxheight);


		# Тип генерации фона из конфигурации
		if(isset($config->gd_thumb_type_gen) && $config->gd_thumb_type_gen == "size") {
			$this->thumbtg = "size";
		}


		# Фоновый цвет  из конфигурации
		if(isset($config->gd_thumb_bgcolor) && mb_strlen($config->gd_thumb_bgcolor) == 7) {
			$this->thumbbgcol = $parse->cvrt_color_h2d($config->gd_thumb_bgcolor);
		}


		# Качество миниатюр  из конфигурации
		if(isset($config->gd_thumb_jpg_quality) && round($config->gd_thumb_jpg_quality) >= 10 && round($config->gd_thumb_jpg_quality) <= 100) {
			$this->th_quality = round($config->gd_thumb_jpg_quality);
		}


		# Если используем watermark
		if(isset($config->gd_use_watermark) && $config->gd_use_watermark) {

			# watermark string one
			if(trim($config->gd_watermark_string_one) != "") {
				$this->copyright = $parse->text->html($config->gd_watermark_string_one);
			}
			else $this->copyright =& $site['title'];

			# watermark string two
			if(trim($config->gd_watermark_string_two) != "") {
				$this->domain = $parse->text->html($config->gd_watermark_string_two);
			}
			else $this->domain = $_SERVER['SERVER_NAME'];
		}
	}


	/**
	* Изменяем размер изображения, если оно превышает допустимый администратором.
	*
	* @param string $filename	- Имя файла изображения
	* @param string $ext		- Расширение файла без точки
	* @param path|string $path	- Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	*/
	protected function resize($filename, $ext, $path=_UPLOADIMAGES) {

		# vars
        	$fileoriginal 	= $filename."_original.".$ext;
        	$fileresize 	= $filename."_resize.".$ext;

		# определяем размер картинки
		$size = getimagesize($path."/".$fileoriginal);
		$w = $size[0];
		$h = $size[1];

		if($w <= $this->msize['w'] && $h <= $this->msize['h']) {
			copy($path."/".$fileoriginal, $path."/".$fileresize);
		}
		else {
			# Проводим расчеты по сжатию и уменьшению в размерах
			$ns = $this->calc_resize($w, $h, $this->msize['w'], $this->msize['h']);

			# вносим в память пустую превью и оригинальный файл, для дальнейшего издевательства над ними.
			$resize 	= $this->imgcreatetruecolor($ns['new_width'], $ns['new_height'], $ext);

	        	$alpha 		= ($ext == "png" || $ext == "gif") ? 127 : 0 ;
	        	$bgcolor 	= imagecolorallocatealpha($resize, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

	        	# alpha
			if($ext == "gif" || $ext == "png") {
				imagecolortransparent($resize, $bgcolor);
			}

			imagefilledrectangle($resize, 0, 0, $ns['new_width']-1, $ns['new_height']-1, $bgcolor);

			# вводим в память файл для издевательств
			$src = $this->imgcreate($path."/".$fileoriginal, $ext);

            		imagecopyresampled($resize, $src, 0, 0, 0, 0, $ns['new_width'], $ns['new_height'], $w, $h);

			# льем измененное изображение
			if($ext == "jpg")	imagejpeg($resize,$path."/".$fileresize, $this->rs_quality);
			elseif($ext == "gif")	imagegif($resize,$path."/".$fileresize);
			elseif($ext == "png")	imagepng($resize,$path."/".$fileresize);
			imagedestroy($resize);
			imagedestroy($src);
		}
	}


	/**
	* Генерируем миниатюру изображения для предпросмотра.
	*
	* @param string $filename	- Имя файла изображения
	* @param string $ext		- Расширение файла без точки
	* @param path|string $path	- Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	*/
	protected function thumbnail($filename, $ext, $path=_UPLOADIMAGES) {

		# vars
        	$fileresize 	= $filename."_resize.".$ext;
        	$filethumb 	= $filename."_thumb.".$ext;

		# определяем размер картинки
		$size = getimagesize($path."/".$fileresize);
		$w = $size[0];
		$h = $size[1];

		# вносим в память пустую превью и оригинальный файл, для дальнейшего издевательства над ними.
		$thumb		= $this->imgcreatetruecolor($this->tsize['w'], $this->tsize['h'], $ext);

		$alpha 		= ($ext == "png" || $ext == "gif") ? 127 : 0 ;
        	$bgcolor	= imagecolorallocatealpha($thumb, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

		# alpha
		if($ext == "gif" || $ext == "png") {
			imagecolortransparent($thumb, $bgcolor);
		}

		imagefilledrectangle($thumb, 0, 0, $this->tsize['w']-1, $this->tsize['h']-1, $bgcolor);

		# вводим в память файл для издевательств
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# Проводим расчеты по сжатию превью и уменьшению в размерах
		$ns = $this->calc_resize($w, $h, $this->tsize['w'], $this->tsize['h']);

		# Перерасчет для заливки превью
		if($this->thumbtg == "fill") {
			if($ns['new_left'] > 0) {
				$ns['new_top'] = $ns['new_top'] - $ns['new_left'];
				$proc = (($ns['new_left'] * 2) / $ns['new_width']);
				$ns['new_width']	= ($ns['new_width'] + ($ns['new_width'] * $proc)) + 2;
				$ns['new_height']	= ($ns['new_height'] + ($ns['new_height'] * $proc)) + 2;
				$ns['new_left'] = 0;
			}

			if($ns['new_top'] > 0) {
				$ns['new_left'] = $ns['new_left'] - $ns['new_top'];
				$proc = (($ns['new_top'] * 2) / $ns['new_height']);
				$ns['new_width']	= ($ns['new_width'] + ($ns['new_width'] * $proc)) + 2;
				$ns['new_height']	= ($ns['new_height'] + ($ns['new_height'] * $proc)) + 2;
				$ns['new_top'] = 0;
			}
		}

		imagecopyresampled($thumb, $src, $ns['new_left'], $ns['new_top'], 0, 0, $ns['new_width'], $ns['new_height'], $w, $h);

		# льем превью
		if($ext == "jpg")	imagejpeg($thumb,$path."/".$filethumb, $this->th_quality);
		elseif($ext == "gif")	imagegif($thumb,$path."/".$filethumb);
		elseif($ext == "png")	imagepng($thumb,$path."/".$filethumb);
		imagedestroy($thumb);
		imagedestroy($src);
	}


	/* ####################################################
	 * Функция генерация водяного знака на изображении
	 * 	$filename	-	[string]	Имя файла
	 * 	$ext		-	[string]	Расширение файла без точки
	 * 	$path		-	[string]	Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	 */
	protected function watermark($filename, $ext, $path=_UPLOADIMAGES) {

		# vars
        	$fileresize 	= $filename."_resize.".$ext;

		# определяем размер картинки
		$size = getimagesize($path."/".$fileresize);
		$w = $size[0];
		$h = $size[1];

		# вводим в память файл для издевательств
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# удаляем оригинал
		unlink($path."/".$fileresize);


		// Gaussian blur matrix:
		// $matrix = array(
			// array( 1, 2, 2 ),
			// array( 2, 4, 2 ),
			// array( 1, 2, 1 )
		// );
		// imageconvolution($src, $matrix, 16, 0);


		# наклон
		$angle = 0;

		# Тень следом текст, далее цвет линии подложки
		$shadow 	= imagecolorallocatealpha($src, 0, 0, 0, 20);
		$color  	= imagecolorallocatealpha($src, 255, 255, 255, 20);
		$colorline	= imagecolorallocatealpha($src, 220, 220, 225, 66);

		# размер шрифта
		$size = 10;

		# выбираем шрифт
		$fontfile = ""._ROOCMS."/fonts/trebuc.ttf";
		//$fontfile = ""._ROOCMS."/fonts/verdana.ttf";

		//imagefilledrectangle($src, 0, $h-33, $w, $h-5, $colorline);
		//imagettfbbox($size, $angle, $fontfile, $this->domain);

		//$this->copyright = $this->tounicode($this->copyright);
		if(trim($this->copyright) != "") {
			imagettftext($src, $size, $angle, 7+1, $h-18+1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $size, $angle, 7-1, $h-18-1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $size, $angle, 7+1, $h-18-1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $size, $angle, 7-1, $h-18+1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $size, $angle, 7, $h-18, $color, $fontfile, $this->copyright);
		}

		//$this->domain = $this->tounicode($this->domain);
		if(trim($this->domain) != "") {
			imagettftext($src, $size, $angle, 7+1, $h-5+1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $size, $angle, 7-1, $h-5-1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $size, $angle, 7+1, $h-5-1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $size, $angle, 7-1, $h-5+1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $size, $angle, 7, $h-5, $color, $fontfile, $this->domain);
		}



		# вливаем с ватермарком
		if($ext == "jpg")	imagejpeg($src,$path."/".$fileresize, $this->rs_quality);
		elseif($ext == "gif")	imagegif($src,$path."/".$fileresize);
		elseif($ext == "png")	imagepng($src,$path."/".$fileresize);

        	imagedestroy($src);
	}


	/**
	* put your comment there...
	*
	* @param string $from	- полный путь и имя файла из которого будем крафтить изображение
	* @param string $ext	- расширение файла без точки
	* @return data - функция вернет идентификатор (сырец) для работы (издевательств) с изображением.
	*/
	private function imgcreate($from, $ext) {

		switch($ext) {
			case 'jpg':
                	        $src = imagecreatefromjpeg($from);
			        break;

			case 'gif':
                		$src = imagecreatefromgif($from);
				break;

			case 'png':
                		$src = imagecreatefrompng($from);
				break;

			/* default:
				$src = imagecreatefromjpeg($path);
				break; */
		}

		if($ext == "png" || $ext == "gif") {
	                imagealphablending($src, false);
			imagesavealpha($src,true);
		}

		return $src;
	}



	private function imgcreatetruecolor($width, $height, $ext) {

                $src = imagecreatetruecolor($width, $height);

		if($ext == "png" || $ext == "gif") {
	                imagealphablending($src, false);
			imagesavealpha($src,true);
		}

		return $src;
	}


	/**
	* Расчитываем новые размеры изображений
	*
	* @param int $width		- Текущая ширина
	* @param int $height	- Текущая высота
	* @param int $towidth	- Требуемая ширина
	* @param int $toheight	- Требуемая высота
	* @return array	- Функция возвращает массив с ключами ['new_width'] - новая ширина, ['new_height'] - новая высота, ['new_left'] - значение позиции слева, ['new_top'] - значение позиции сверху
	*/
	private function calc_resize($width, $height, $towidth, $toheight) {

		$x_ratio 	= $towidth / $width;
		$y_ratio 	= $toheight / $height;
		$ratio 		= min($x_ratio, $y_ratio);
		$use_x_ratio 	= ($x_ratio == $ratio);
		$new_width 	= $use_x_ratio 	? $towidth : floor($width * $ratio);
		$new_height 	= !$use_x_ratio ? $toheight : floor($height * $ratio);
		$new_left 	= $use_x_ratio 	? 0 : floor(($towidth - $new_width) / 2);
		$new_top 	= !$use_x_ratio ? 0 : floor(($toheight - $new_height) / 2);

		$return = array();
		$return = array('new_width'	=> $new_width,
				'new_height'	=> $new_height,
				'new_left'	=> $new_left,
				'new_top'	=> $new_top);

		return $return;
	}
}

?>