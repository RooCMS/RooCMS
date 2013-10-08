<?php
/**
* @package		RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	GD Class
* @author		alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link			http://www.roocms.com
* @version		1.2.5
* @since		$date$
* @license		http://www.gnu.org/licenses/gpl-3.0.html
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


//	graphic class :: GD

$gd = new GD;

class GD {

    # vars
	var $info		= array();								# Информация о GD расширении
	var $copyright	= "";									# Текст копирайта ( По умолчанию: $site['title'] )
	var $domain		= "";									# Адрес домена ( По умолчанию: $site['domain'] )
	var $msize		= array('w' => 900,'h' => 900);			# Максимальные размеры сохраняемого изображения
	var $tsize		= array('w' => 100,'h' => 100);			# Размеры миниатюры
	var $rs_quality	= 90;									# Качество обработанных изображений
	var $th_quality	= 90;									# Качество генерируемых миниматюр
	var $thumbtg	= "fill";								# Тип генерируемой миниатюры ( Возможные значения: fill - заливка, size - по размеру изображения )
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
		if(isset($config->gd_image_maxwidth) && round($config->gd_image_maxwidth) >= 32 && round($config->gd_image_maxwidth) > $this->tsize['w'])		$this->msize['w'] = round($config->gd_image_maxwidth);
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
	* Загрузка картинок через $_POST
	*
	* @param string $file - имя в массиве $_FILES
	* @param string $prefix - префикс для имения файла.
	* @param array $thumbsize - array(width,height) - размеры миниатюры будут изменен согласно параметрам.
	* @param boolean $watermark - флаг указывает наносить ли водяной знак на рисунок.
	* @param string $path - путь к папке для загрузки изображений.
	* @param boolean $no_empty - определяет пропускать ли пустые элементы в массиве FILES или обозначать их в выходном буфере.
	* @return string or array - возвращает имя файла или массив с именами файлов.
	*/
	public function upload_image($file, $prefix="", $thumbsize=array(), $watermark=true, $path=_UPLOADIMAGES, $no_empty=true) {

		global $config, $files;


		# check allow type
		require_once _LIB."/mimetype.php";

		static $allow_exts = array();

		if(empty($allow_exts)) {
	        foreach($imagetype AS $itype) {
        		$allow_exts[$itype['type']] = $itype['ext'];
			}
		}


		# Resize ini vars
		if(is_array($thumbsize) && count($thumbsize) == 2 && round($thumbsize[0]) > 0 && round($thumbsize[1]) > 0) {
			$this->tsize['w'] = round($thumbsize[0]);
			$this->tsize['h'] = round($thumbsize[1]);
		}


		# Если $_FILES не является массивом
		if(!is_array($_FILES[$file]['tmp_name'])) {
			if(isset($_FILES[$file]['tmp_name']) && $_FILES[$file]['error'] == 0) {

				$upload = false;

				# Грузим апельсины бочками
				if(array_key_exists($_FILES[$file]['type'], $allow_exts)) {

					# Создаем имя файлу.
					$ext = $allow_exts[$_FILES[$file]['type']];
					$filename = $files->create_filename($ext, $prefix);

					# Сохраняем оригинал
					copy($_FILES[$file]['tmp_name'], $path."/original/".$filename);

					# Если загрузка прошла и файл на месте
					$upload = true;
					if(!file_exists($path."/original/".$filename)) $upload = false;
				}

				# Если загрузка удалась
				if($upload) {

            		# изменяем изображение если, оно превышает допустимые размеры
                	$this->resize($filename, $ext, $path);

					# Создаем миниатюру
					$this->thumbnail($filename, $ext, $path);

					if($config->gd_use_watermark && $watermark) {
						# наносим ватермарк
						$this->watermark($filename, $ext, $path);
					}
				}
				else {
					# Обработчик если загрузка не удалась =)
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
		# Если $_FILES является массивом
		else {
			foreach($_FILES[$file]['tmp_name'] AS $key=>$value) {
				if(isset($_FILES[$file]['tmp_name'][$key]) && $_FILES[$file]['error'][$key] == 0) {

					$upload = false;

					# Грузим апельсины бочками
					if(array_key_exists($_FILES[$file]['type'][$key], $allow_exts)) {

						# Создаем имя файлу.
						$ext = $allow_exts[$_FILES[$file]['type'][$key]];
						$filename = $files->create_filename($ext, $prefix);

						# Сохраняем оригинал
						copy($_FILES[$file]['tmp_name'][$key], $path."/original/".$filename);

						# Если загрузка прошла и файл на месте
						$upload = true;
						if(!file_exists($path."/original/".$filename)) $upload = false;
					}

					# Если загрузка удалась
					if($upload) {

                        # изменяем изображение если, оно превышает допустимые размеры
                        $this->resize($filename, $ext, $path);

						# Создаем миниатюру
						$this->thumbnail($filename, $ext, $path);

						if($config->gd_use_watermark && $watermark) {
							# наносим ватермарк
							$this->watermark($filename, $ext, $path);
						}
					}
					else {
						# Обработчик если загрузка не удалась =)
						$filename = false;
					}
				}
				else {
					// вписать сообщение об ошибке.
					// впрочем ещё надо и обработчик ошибок написать.
					$filename = false;
				}

				if(!$no_empty) {
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


	/**
	* Изменяем размер изображения, если оно превышает допустимый администратором.
	*
	* @param string $filename	- Имя файла изображения
	* @param string $ext		- Расширение файла без точки
	* @param path|string $path	- Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	*/
	public function resize($filename, $ext, $path=_UPLOADIMAGES) {

		# определяем размер картинки
		$size = getimagesize($path."/original/".$filename);
		$w = $size[0];
		$h = $size[1];

		if($w <= $this->msize['w'] && $h <= $this->msize['h']) {
			copy($path."/original/".$filename, $path."/resize/".$filename);
		}
		else {
			# Проводим расчеты по сжатию и уменьшению в размерах
			$ns = $this->calc_resize($w, $h, $this->msize['w'], $this->msize['h']);

			# вносим в память пустую превью и оригинальный файл, для дальнейшего издевательства над ними.
			$resize 	= imagecreatetruecolor($ns['new_width'], $ns['new_height']);
	        $bgcolor 	= imagecolorallocatealpha($resize, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], 0);    //127

	        # alpha
			if($ext == "gif" || $ext == "png") {
				imagecolortransparent($resize, $bgcolor);
			}

			imagefilledrectangle($resize, 0, 0, $ns['new_width']-1, $ns['new_height']-1, $bgcolor);

			# вводим в память файл для издевательств
			$src = $this->imgcreate($path."/original/".$filename, $ext);

            imagecopyresampled($resize, $src, 0, 0, 0, 0, $ns['new_width'], $ns['new_height'], $w, $h);

			# льем измененное изображение
			if($ext == "jpg")		imagejpeg($resize,$path."/resize/".$filename, $this->rs_quality);
			elseif($ext == "gif")	imagegif($resize,$path."/resize/".$filename);
			elseif($ext == "png")	imagepng($resize,$path."/resize/".$filename);
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
	public function thumbnail($filename, $ext, $path=_UPLOADIMAGES) {

		# определяем размер картинки
		$size = getimagesize($path."/resize/".$filename);
		$w = $size[0];
		$h = $size[1];

		# вносим в память пустую превью и оригинальный файл, для дальнейшего издевательства над ними.
		$thumb 		= imagecreatetruecolor($this->tsize['w'], $this->tsize['h']);
        $bgcolor 	= imagecolorallocatealpha($thumb, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], 0);

        # alpha
		if($ext == "gif" || $ext == "png") {
			imagecolortransparent($thumb, $bgcolor);
		}

		imagefilledrectangle($thumb, 0, 0, $this->tsize['w']-1, $this->tsize['h']-1, $bgcolor);

		# вводим в память файл для издевательств
		$src = $this->imgcreate($path."/resize/".$filename, $ext);

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
		if($ext == "jpg")		imagejpeg($thumb,$path."/thumb/".$filename, $this->th_quality);
		elseif($ext == "gif")	imagegif($thumb,$path."/thumb/".$filename);
		elseif($ext == "png")	imagepng($thumb,$path."/thumb/".$filename);
		imagedestroy($thumb);
		imagedestroy($src);
	}


	/* ####################################################
	 * Функция генерация водяного знака на изображении
	 * 	$filename	-	[string]	Имя файла
	 * 	$ext		-	[string]	Расширение файла без точки
	 * 	$path		-	[string]	Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	 */
	private function watermark($filename, $ext, $path=_UPLOADIMAGES) {

		# определяем размер картинки
		$size = getimagesize($path."/resize/".$filename);
		$w = $size[0];
		$h = $size[1];

		# вводим в память файл для издевательств
		$src = $this->imgcreate($path."/resize/".$filename, $ext);

		# удаляем оригинал
		unlink($path."/resize/".$filename);


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
		$colorline  = imagecolorallocatealpha($src, 220, 220, 225, 66);

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
		if($ext == "jpg")		imagejpeg($src,$path."/resize/".$filename, $this->rs_quality);
		elseif($ext == "gif")	imagegif($src,$path."/resize/".$filename);
		elseif($ext == "png")	imagepng($src,$path."/resize/".$filename);

        imagedestroy($src);
	}


	/* ####################################################
	 * Преобразовываем текст из ISO8859-5 в Unicode
	 * Использовать перед запуском imagettftext
	 * [Морально устаревшая функция после перехода на utf8]
	 */
	protected function tounicode($text, $from="w") {
		$text = convert_cyr_string($text, $from, "i");
		$uni  = "";
		for($i = 0, $len = mb_strlen($text, 'utf8'); $i < $len; $i++) {
			$char = $text{$i};
			$code = ord($char);
			$uni .= ($code > 175)? "&#" . (1040 + ($code - 176)) . ";" : $char;
		}

		return $uni;
	}


	/**
	* put your comment there...
	*
	* @param string $path	- полный путь и имя файла из которого будем крафтить изображение
	* @param string $ext	- расширение файла без точки
	* @return data - функция вернет идентификатор (сырец) для работы (издевательств) с изображением.
	*/
	private function imgcreate($path, $ext) {

		switch($ext) {
			case 'jpg':
                $src = imagecreatefromjpeg($path);
				break;

			case 'gif':
                $src = imagecreatefromgif($path);
				break;

			case 'png':
                $src = imagecreatefrompng($path);
				break;

			/*default:
				$src = imagecreatefromjpeg($path);
				break;*/
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

		$x_ratio 		= $towidth / $width;
		$y_ratio 		= $toheight / $height;
		$ratio 			= min($x_ratio, $y_ratio);
		$use_x_ratio 	= ($x_ratio == $ratio);
		$new_width 		= $use_x_ratio 	? $towidth : floor($width * $ratio);
		$new_height 	= !$use_x_ratio ? $toheight : floor($height * $ratio);
		$new_left 		= $use_x_ratio 	? 0 : floor(($towidth - $new_width) / 2);
		$new_top 		= !$use_x_ratio ? 0 : floor(($toheight - $new_height) / 2);

		$return = array();
		$return = array('new_width'		=> $new_width,
						'new_height'	=> $new_height,
						'new_left'		=> $new_left,
						'new_top'		=> $new_top);

		return $return;
	}
}

?>