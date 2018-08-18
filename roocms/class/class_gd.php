<?php
/**
 *   RooCMS - Open Source Free Content Managment System
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
* @package	RooCMS
* @subpackage	Engine RooCMS classes
* @author	alex Roosso
* @copyright	2010-2019 (c) RooCMS
* @link		http://www.roocms.com
* @version	1.16.1
* @since	$date$
* @license	http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class GD
 */
class GD {

	# vars
	public $info		= [];					# Информация о GD расширении
	public $copyright	= "";					# Текст копирайта ( По умолчанию: $site['title'] )
	public $domain		= "";					# Адрес домена ( По умолчанию: $site['domain'] )
	public $msize		= array('w' => 900,'h' => 900);		# Максимальные размеры сохраняемого изображения
	public $tsize		= array('w' => 267,'h' => 150);		# Размеры миниатюры
	private $rs_quality	= 90;					# Качество обработанных изображений
	private $th_quality	= 90;					# Качество генерируемых миниматюр
	private $thumbtg	= "cover";				# Тип генерируемой миниатюры ( Возможные значения: cover - заливка, contain - по размеру изображения )
	private $thumbbgcol	= array('r' => 0, 'g' => 0, 'b' => 0);	# Значение фонового цвета, если тип генерируемых миниатюр производится по размеру ( $thumbtg = size )



	/**
	* Let's go
	*/
	public function __construct() {

		global $config, $site, $parse;

		# Получить GD info
		$this->info = gd_info();

		# Устанавливаем размеры миниатюр из конфигурации
		if(isset($config->gd_thumb_image_width, $config->gd_thumb_image_height)) {
			$this->tsize = $this->set_mod_sizes(array($config->gd_thumb_image_width, $config->gd_thumb_image_height));
		}

		# Устанавливаем максимальные размеры изображений
		if(isset($config->gd_image_maxwidth, $config->gd_image_maxheight)) {
			$this->msize = $this->set_mod_sizes(array($config->gd_image_maxwidth, $config->gd_image_maxheight));
		}

		# Тип генерации фона из конфигурации
		if(isset($config->gd_thumb_type_gen) && $config->gd_thumb_type_gen == "contain") {
			$this->thumbtg = "contain";
		}

		# Фоновый цвет  из конфигурации
		if(isset($config->gd_thumb_bgcolor) && mb_strlen($config->gd_thumb_bgcolor) == 7) {
			$this->thumbbgcol = $parse->cvrt_color_h2d($config->gd_thumb_bgcolor);
		}

		# Качество миниатюр  из конфигурации
		if(isset($config->gd_thumb_jpg_quality) && $config->gd_thumb_jpg_quality >= 10 && $config->gd_thumb_jpg_quality <= 100) {
			$this->th_quality = $config->gd_thumb_jpg_quality;
		}

		# Если используем watermark
		if(isset($config->gd_use_watermark) && $config->gd_use_watermark == "text") {

			# watermark text string one
			if(trim($config->gd_watermark_string_one) != "") {
				$this->copyright = $parse->text->html($config->gd_watermark_string_one);
			}
			else {
				$this->copyright = $parse->text->html($site['title']);
			}

			# watermark text string two
			if(trim($config->gd_watermark_string_two) != "") {
				$this->domain = $parse->text->html($config->gd_watermark_string_two);
			}
			else {
				$this->domain = $_SERVER['SERVER_NAME'];
			}
		}
	}


	/**
	 * Функция проводит стандартные операции над загруженным файлом.
	 * Изменяет размеры, создает миниатюру, наносит водяной знак.
	 *
	 * @param       $filename  - Имя файла (без расширения)
	 * @param       $extension - расширение файла
	 * @param       $path      - путь к расположению файла.
	 * @param array $options
	 * @internal param bool $watermark 	- флаг указывает наносить ли водяной знак на рисунок.
	 * @internal param bool $modify 	- флаг указывает подвергать ли изображение полной модификации с сохранением оригинального изображения и созданием превью.
	 * @internal param bool $noresize 	- флаг указывает подвергать ли изображение изменению размера. Иcпользуется в том случае когда мы не хотим изменять оригинальное изображение.
	 */
	protected function modify_image($filename, $extension, $path, array $options=array("watermark"=>true, "modify"=>true, "noresize"=>false)) {

		global $config;


		# Модифицируем?
		if(isset($options['modify']) && $options['modify']) {
			# изменяем изображение если, оно превышает допустимые размеры
			$this->resize($filename, $extension, $path);

			# Создаем миниатюру
			$this->thumbnail($filename, $extension, $path);
		}
		else {
			if(!isset($options['noresize']) || !$options['noresize']) {
				$this->resized($filename, $extension, $path);
			}
		}


		# Наносим ватермарк
		if($config->gd_use_watermark != "no" && (isset($options['watermark']) && $options['watermark'])) {

			# Текстовый watermark
			if($config->gd_use_watermark == "text" ) {
				$this->watermark_text($filename, $extension, $path);
			}

			# Графический watermark
			if($config->gd_use_watermark == "image" ) {
				$this->watermark_image($filename, $extension, $path);
			}
		}
	}


	/**
	 * Изменяем размер изображения, если оно превышает допустимый администратором.
	 *
	 * @param string      $filename - Имя файла изображения
	 * @param string      $ext      - Расширение файла без точки
	 * @param path|string $path     - Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
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
			switch($ext) {
				case 'jpg':
					imagejpeg($resize,$path."/".$fileresize, $this->rs_quality);
					break;

				case 'gif':
					imagegif($resize,$path."/".$fileresize);
					break;

				case 'png':
					imagepng($resize,$path."/".$fileresize);
					break;
			}

			imagedestroy($resize);
			imagedestroy($src);
		}
	}


	/**
	 * Изменяем размер изображения.
	 *
	 * @param string      $filename - Имя файла изображения
	 * @param string      $ext      - Расширение файла без точки
	 * @param path|string $path     - Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	 */
	protected function resized($filename, $ext, $path=_UPLOADIMAGES) {

		# vars
		$file = $filename.".".$ext;

		# определяем размер картинки
		$size = getimagesize($path."/".$file);

		# пробуем определить ориентацию изображения
		$orientation = $this->get_orientation($path."/".$file);

		# вносим в память пустую превью и оригинальный файл, для дальнейшего издевательства над ними.
		$resize  = $this->imgcreatetruecolor($this->tsize['w'], $this->tsize['h'], $ext);
		$alpha   = ($ext == "png" || $ext == "gif") ? 127 : 0;

		$bgcolor = imagecolorallocatealpha($resize, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

		# alpha
		if($ext == "gif" || $ext == "png") {
			imagecolortransparent($resize, $bgcolor);
		}

		imagefilledrectangle($resize, 0, 0, $this->tsize['w']-1, $this->tsize['h']-1, $bgcolor);

		# вводим в память файл для издевательств
		$src = $this->imgcreate($path."/".$file, $ext);
		# ... и удаляем
		unlink($path."/".$file);

		# Проводим расчеты по сжатию превью и уменьшению в размерах
		$ns = $this->calc_resize($size[0], $size[1], $this->tsize['w'], $this->tsize['h'], false);
		$ns = $this->calc_newsize($ns);


		imagecopyresampled($resize, $src, $ns['new_left'], $ns['new_top'], 0, 0, $ns['new_width'], $ns['new_height'], $size[0], $size[1]);


		# Переворачиваем изображение, если в этом есть необходимость
		switch($orientation) {
			case 3:
				$resize = imagerotate($resize, 180, 0);
				break;
			case 6:
				$resize = imagerotate($resize, -90, 0);
				break;
			case 8:
				$resize = imagerotate($resize, 90, 0);
				break;
		}

		# льем превью
		switch($ext) {
			case 'jpg':
				imagejpeg($resize,$path."/".$file, $this->th_quality);
				break;

			case 'gif':
				imagegif($resize,$path."/".$file);
				break;

			case 'png':
				imagepng($resize,$path."/".$file);
				break;
		}

		imagedestroy($resize);
		imagedestroy($src);

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
		$resize = ($this->thumbtg != "cover") ? true : false ;
		$ns = $this->calc_resize($size[0], $size[1], $this->tsize['w'], $this->tsize['h'], $resize);

		# Перерасчет для заливки превью
		if($this->thumbtg == "cover") {
			$ns = $this->calc_newsize($ns);
		}

		imagecopyresampled($thumb, $src, $ns['new_left'], $ns['new_top'], 0, 0, $ns['new_width'], $ns['new_height'], $size[0], $size[1]);

		# льем превью
		switch($ext) {
			case 'jpg':
				imagejpeg($thumb,$path."/".$filethumb, $this->th_quality);
				break;

			case 'gif':
				imagegif($thumb,$path."/".$filethumb);
				break;

			case 'png':
				imagepng($thumb,$path."/".$filethumb);
				break;
		}

		imagedestroy($thumb);
		imagedestroy($src);
	}


	/**
	 * Функция генерация водяного знака на изображении
	 *
	 * @param string $filename - Имя файла
	 * @param string $ext - Расширение файла без точки
	 * @param string $path - Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	 */
	protected function watermark_text($filename, $ext, $path=_UPLOADIMAGES) {

		# vars
        	$fileresize = $filename."_resize.".$ext;

		# определяем размер картинки
		$size = getimagesize($path."/".$fileresize);
		$w = $size[0];
		$h = $size[1];

		# вводим в память файл для издевательств
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# удаляем оригинал
		unlink($path."/".$fileresize);


		# Gaussian blur matrix:
		/*
		$matrix = array(
			array( 1, 2, 2 ),
			array( 2, 4, 2 ),
			array( 1, 2, 1 )
		);
		imageconvolution($src, $matrix, 16, 0);	*/


		# наклон
		$angle = 0;

		# Тень следом текст, далее цвет линии подложки
		$shadow 	= imagecolorallocatealpha($src, 0, 0, 0, 20);
		$color  	= imagecolorallocatealpha($src, 255, 255, 255, 20);

		# размер шрифта
		$size = 10;

		# выбираем шрифт
		$fontfile = ""._ROOCMS."/fonts/trebuc.ttf";

		if(trim($this->copyright) != "") {
			imagettftext($src, $size, $angle, 7+1, $h-18+1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $size, $angle, 7-1, $h-18-1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $size, $angle, 7+1, $h-18-1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $size, $angle, 7-1, $h-18+1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $size, $angle, 7, $h-18, $color, $fontfile, $this->copyright);
		}

		if(trim($this->domain) != "") {
			imagettftext($src, $size, $angle, 7+1, $h-5+1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $size, $angle, 7-1, $h-5-1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $size, $angle, 7+1, $h-5-1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $size, $angle, 7-1, $h-5+1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $size, $angle, 7, $h-5, $color, $fontfile, $this->domain);
		}

		# вливаем с ватермарком
		switch($ext) {
			case 'jpg':
				imagejpeg($src,$path."/".$fileresize, $this->rs_quality);
				break;

			case 'gif':
				imagegif($src,$path."/".$fileresize);
				break;

			case 'png':
				imagepng($src,$path."/".$fileresize);
				break;
		}

        	imagedestroy($src);
	}


	/**
	 * Функция генерация водяного знака на изображении
	 *
	 * @param string $filename - Имя файла
	 * @param string $ext - Расширение файла без точки
	 * @param string $path - Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	 */
	protected function watermark_image($filename, $ext, $path=_UPLOADIMAGES) {

		global $config, $parse;

		# vars
		$fileresize = $filename."_resize.".$ext;

		# определяем размер картинки
		$size = getimagesize($path."/".$fileresize);
		$w = $size[0];
		$h = $size[1];

		# вводим в память файл для издевательств
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# удаляем оригинал
		unlink($path."/".$fileresize);

		# watermark
		$wminfo = pathinfo($path."/".$config->gd_watermark_image);
		$wmsize = getimagesize($path."/".$config->gd_watermark_image);
		$ww = $wmsize[0];
		$wh = $wmsize[1];
		$watermark = $this->imgcreate($path."/".$config->gd_watermark_image, $wminfo['extension']);


		# Расчитываем не будет ли выглядеть большим ватермарк на изображении.
		$maxwmw = floor($w*0.33); $wp = 0;
		if($ww >= $maxwmw) {
			$wp = $parse->percent($maxwmw, $ww);
		}

		$maxwmh = floor($h*0.33); $hp = 0;
		if($wh >= $maxwmh) {
			$hp = $parse->percent($maxwmh, $wh);
		}

		if($wp != 0 || $hp != 0) {
			$pr = max($wp, $hp)/100;
		}
		else {
			$pr = 1;
		}


		$wms = $this->calc_resize($ww, $wh, $ww*$pr, $wh*$pr, false);


		$x = $w - ($wms['new_width'] + 10);
		$y = $h - ($wms['new_height'] + 10);


		//imagecopyresampled($src, $watermark, $x, $y, 0, 0, $wms['new_width'], $wms['new_height'], $ww, $wh);
		imagecopyresized($src, $watermark, $x, $y, 0, 0, $wms['new_width'], $wms['new_height'], $ww, $wh);


		# вливаем с ватермарком
		switch($ext) {
			case 'jpg':
				imagejpeg($src,$path."/".$fileresize, $this->rs_quality);
				break;

			case 'gif':
				imagegif($src,$path."/".$fileresize);
				break;

			case 'png':
				imagepng($src,$path."/".$fileresize);
				break;
		}

		imagedestroy($src);
		imagedestroy($watermark);
	}


	/**
	 * Функция создает исходник из готового изображения для дальнейшей с ним работы (обработки).
	 *
	 * @param string $from	- полный путь и имя файла из которого будем крафтить изображение
	 * @param string $ext	- расширение файла без точки
	 *
	 * @return resource
	 */
	private function imgcreate($from, $ext) {

		switch($ext) {
			case 'jpg':
                	        $src = imagecreatefromjpeg($from);
			        break;

			case 'gif':
                		$src = imagecreatefromgif($from);
				imagealphablending($src, false);
				imagesavealpha($src,true);
				break;

			case 'png':
                		$src = imagecreatefrompng($from);
				imagealphablending($src, true);
				imagesavealpha($src,true);
				break;

			default:
				$src = imagecreatefromjpeg($from);
				break;
		}

		return $src;
	}


	/**
	 * Функция создает пустой исходник изображения.
	 *
	 * @param int $width	- Ширина создаеваемого изображения
	 * @param int $height	- Высота создаеваемого изображения
	 * @param str $ext	- Расширение создаеваемого изображения
	 *
	 * @return resource	-
	 */
	private function imgcreatetruecolor($width, $height, $ext) {

                $src = imagecreatetruecolor($width, $height);

		if($ext == "png" || $ext == "gif") {
	                imagealphablending($src, false);
			imagesavealpha($src,true);
		}

		return $src;
	}


	/**
	 * Получаем ориентацию изображения
	 *
	 * @param $image - указываем изображение
	 *
	 * @return int
	 */
	private function get_orientation($image) {

		$orientation = 1;

		if(function_exists('exif_read_data') && exif_imagetype($image) == 2) {
			$exif = exif_read_data($image);
			if(isset($exif['Orentation'])) {
				$orientation = $exif['Orentation'];
			}
		}

		return $orientation;
	}


	/**
	 * Расчитываем новые размеры изображений
	 *
	 * @param int  $width    - Текущая ширина
	 * @param int  $height   - Текущая высота
	 * @param int  $towidth  - Требуемая ширина
	 * @param int  $toheight - Требуемая высота
	 * @param bool $resize   - Флаг указывающий производим мы пропорциональное изменение или образание. True - производим расчеты для пропорционального изменения. False - производим обрезание (crop)
	 *
	 * @return array<int> - Функция возвращает массив с ключами ['new_width'] - новая ширина, ['new_height'] - новая высота, ['new_left'] - значение позиции слева, ['new_top'] - значение позиции сверху
	 */
	private function calc_resize($width, $height, $towidth, $toheight, $resize = true) {

		$x_ratio 	= $towidth / $width;
		$y_ratio 	= $toheight / $height;
		$ratio 		= ($resize) ? min($x_ratio, $y_ratio) : max($x_ratio, $y_ratio);
		$use_x_ratio 	= ($x_ratio == $ratio);
		$new_width 	= $use_x_ratio 	? $towidth : floor($width * $ratio);
		$new_height 	= !$use_x_ratio ? $toheight : floor($height * $ratio);
		$new_left 	= $use_x_ratio 	? 0 : floor(($towidth - $new_width) / 2);
		$new_top 	= !$use_x_ratio ? 0 : floor(($toheight - $new_height) / 2);

		$return = array('new_width'	=> $new_width,
				'new_height'	=> $new_height,
				'new_left'	=> $new_left,
				'new_top'	=> $new_top);

		return $return;
	}


	/**
	 * Корректируем массив с обновленными размерами.
	 *
	 * @param array $ns - array new size
	 *
	 * @return array $ns
	 */
	private function calc_newsize(array $ns) {

		if($ns['new_left'] > 0) {
			$ns['new_top'] = $ns['new_top'] - $ns['new_left'];
			$proc = (($ns['new_left'] * 2) / $ns['new_width']);
			$ns['new_width']  = ($ns['new_width'] + ($ns['new_width'] * $proc)) + 2;
			$ns['new_height'] = ($ns['new_height'] + ($ns['new_height'] * $proc)) + 2;
			$ns['new_left'] = 0;
		}

		if($ns['new_top'] > 0) {
			$ns['new_left'] = $ns['new_left'] - $ns['new_top'];
			$proc = (($ns['new_top'] * 2) / $ns['new_height']);
			$ns['new_width']  = ($ns['new_width'] + ($ns['new_width'] * $proc)) + 2;
			$ns['new_height'] = ($ns['new_height'] + ($ns['new_height'] * $proc)) + 2;
			$ns['new_top'] = 0;
		}

		return $ns;
	}


	/**
	 * Функция устанавливает параметры размеров миниатюр для изображений
	 *
	 * @param array $sizes - array(width,height) - размеры будут изменены согласно параметрам.
	 *
	 * @return array|null
	 */
	protected function set_mod_sizes(array $sizes) {

		if(is_array($sizes) && count($sizes) == 2) {

			$size = [];

			if(round($sizes[0]) > 16) {
				$size['w'] = round($sizes[0]);
			}

			if(round($sizes[1]) > 16) {
				$size['h'] = round($sizes[1]);
			}

			if(!empty($size)) {
				return $size;
			}
		}
	}
}