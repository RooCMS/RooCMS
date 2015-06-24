<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.3.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
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
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
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


/**
 * Class Images
 */
class Images extends GD {


	/**
	 * Раздатчик функции загрузки файлов на сервер и в БД
	 *
	 * @param string  $file - имя в массиве $_FILES
	 * @param string  $prefix - префикс для имения файла.
	 * @param array   $thumbsize - array(width,height) - размеры миниатюры будут изменены согласно параметрам.
	 * @param boolean $watermark - флаг указывает наносить ли водяной знак на рисунок.
	 * @param string  $path - путь к папке для загрузки изображений.
	 * @param boolean $no_empty - определяет пропускать ли пустые элементы в массиве FILES или обозначать их в выходном буфере.
	 *
	 * @return array - возвращает массив с именами файлов.
	 */
	public function upload_image($file, $prefix="", array $thumbsize=array(), $watermark=true, $path=_UPLOADIMAGES, $no_empty=true) {

		return $this->upload_post_image($file, $prefix, $thumbsize, $watermark, $path, $no_empty);
	}


	/**
	 * Загрузка картинок через $_POST
	 *
	 * @param string  $file - имя в массиве $_FILES
	 * @param string  $prefix - префикс для имения файла.
	 * @param array   $thumbsize - array(width,height) - размеры миниатюры будут изменены согласно параметрам.
	 * @param boolean $watermark - флаг указывает наносить ли водяной знак на рисунок.
	 * @param string  $path - путь к папке для загрузки изображений.
	 * @param boolean $no_empty - определяет пропускать ли пустые элементы в массиве FILES или обозначать их в выходном буфере.
	 *
	 * @return array - возвращает массив с именами файлов.
	 */
	public function upload_post_image($file, $prefix="", array $thumbsize=array(), $watermark=true, $path=_UPLOADIMAGES, $no_empty=true) {

		global $files;

		# Составляем массив для проверки разрешенных типов файлов к загрузке
		static $allow_exts = array();
		if(empty($allow_exts))
			$allow_exts = $this->get_allow_exts();


		# Определяем настройки размеров для будущих миниатюр
		$this->set_thumb_sizes($thumbsize);


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

				# Грузим апельсины бочками
				if(array_key_exists($_FILES[$file]['type'][$key], $allow_exts)) {

					# Создаем имя файлу.
					$ext = $allow_exts[$_FILES[$file]['type'][$key]];
					$filename = $files->create_filename($_FILES[$file]['name'][$key], $prefix);

					# Сохраняем оригинал
					copy($_FILES[$file]['tmp_name'][$key], $path."/".$filename."_original.".$ext);

					# Если загрузка прошла и файл на месте
					$upload = (!file_exists($path."/".$filename."_original.".$ext)) ? false : true ;
				}

				# Если загрузка удалась
				if($upload)
					$this->modify_image($filename, $ext, $path, $watermark);
				else {
					# Обработчик если загрузка не удалась =)
					$filename = false;
				}
			}
			else {
				# вписать сообщение об ошибке.
				# впрочем ещё надо и обработчик ошибок написать.
				$filename = false;
			}

			if(!$no_empty) {
				$names[$key] = $filename.".".$ext;
			}
			else {
				if($filename) $names[] = $filename.".".$ext;
			}
		}

		# Возвращаем массив имен файлов для внесения в БД
		return (isset($names) && count($names) > 0) ? $names : false ;
	}


	/**
	 * Обработка картинок согласно установленны в конфигураторе параметрам.
	 * Используется в процедурах смены водянного знака, а так же при мультизагрузке изображений на сервер.
	 *
	 * @param        $source - полный путь к модифицироваемому файлу изображения
	 * @param string $prefix - префикс для имения файла.
	 * @param array  $thumbsize - array(width,height) - размеры миниатюры будут изменен согласно параметрам.
	 * @param bool   $watermark - флаг указывает наносить ли водяной знак на рисунок.
	 * @param string $path - путь к папке для загрузки изображений.
	 * @param bool   $no_empty - определяет пропускать ли пустые элементы в массиве FILES или обозначать их в выходном буфере.
	 *
	 * @return array - возвращает массив с именами файлов.
	 */
	public function reconstruct_image($source, $prefix="", array $thumbsize=array(), $watermark=true, $path=_UPLOADIMAGES, $no_empty=true) {

		global $files;

		# Составляем массив для проверки разрешенных типов файлов к загрузке
		static $allow_exts = array();
		if(empty($allow_exts))
			$allow_exts = $this->get_allow_exts();


		# Определяем настройки размеров для будущих миниатюр
		$this->set_thumb_sizes($thumbsize);


		# Если в параметре для модификации файла мы получили строку, а не массив, преобразовываем в массив.
		if(!is_array($source))
			$sources[] = $source;
		else
			$sources = $source;

		# Приступаем к обработке
		foreach($sources AS $k=>$imagesource) {
			if(file_exists($imagesource)) {

				$upload = false;

				# Грузим апельсины бочками
				$source_info = pathinfo($imagesource);

				if(isset($source_info['extension'])) {
					if(in_array($source_info['extension'], $allow_exts)) {

						# Создаем имя файлу.
						$filename = $files->create_filename(substr($source_info['filename'], 0, -6).".".$source_info['extension'], $prefix);

						# Сохраняем оригинал
						copy($imagesource, $path."/".$filename."_original.".$source_info['extension']);

						# Если загрузка прошла и файл на месте
						$upload = (!file_exists($path."/".$filename."_original.".$source_info['extension'])) ? false : true ;
					}

					# Если загрузка удалась
					if($upload) {
						# modify
						$this->modify_image($filename, $source_info['extension'], $path, $watermark);
						# destroy source
						unlink($imagesource);
					}
					else {
						# Обработчик если загрузка не удалась =)
						$filename = false;
					}


					if(!$no_empty) {
						$names[$key] = $filename.".".$source_info['extension'];
					}
					else {
						if($filename) $names[] = $filename.".".$source_info['extension'];
					}
				}
			}
		}

		# Возвращаем массив имен файлов для внесения в БД
		return (isset($names) && count($names) > 0) ? $names : false ;
	}


	/**
	 * Выгружаем присоедененные изображения
	 *
	 * @param string $where - параметр указывающий на элемент к которому прикреплены изображения
	 * @param int    $from - стартовая позиция для загрузки изображений
	 * @param int    $limit - лимит загружаемых изображений
	 *
	 * @return array $data - массив с данными об изображениях.
	 */
	public function load_images($where, $from = 0, $limit = 0) {

                global $db;

		$data = array();

		$l = ($limit != 0) ? "LIMIT ".$from.",".$limit : "" ;

		$q = $db->query("SELECT id, filename, fileext, sort, alt FROM ".IMAGES_TABLE." WHERE attachedto='".$where."' ORDER BY sort ASC ".$l);
		while($image = $db->fetch_assoc($q)) {
			$image['original']	= $image['filename']."_original.".$image['fileext'];
			$image['resize']	= $image['filename']."_resize.".$image['fileext'];
			$image['thumb']		= $image['filename']."_thumb.".$image['fileext'];

			$data[] = $image;
		}

		return $data;
	}


	/**
	 * Загружаем информацию о изображениях
	 *
	 * @param string $filename - имя файла без $pofix
	 * @param mixed  $attached - родитель файла
	 * @param string $alt - alt-text
	 */
	public function insert_images($filename, $attached, $alt="") {

        	global $db, $parse;

        	//if(!is_array($filename)) $filename[] = $filename;

		$image = pathinfo($filename);

		$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename, fileext, alt)
						    VALUES ('".$attached."', '".$image['filename']."', '".$image['extension']."', '".$alt."')");

		# msg
		if(DEBUGMODE) $parse->msg("Изображение ".$filename." успешно загружено на сервер");
	}


	/**
	 * Функция удаления картинок
	 *
	 * @param int/string $image - указать числовой идентификатор или attachedto
	 * @param boolean    $clwhere - флаг указывает как считывать параметр $image
	 * 				положение false указывает, что передается параметр id или attachedto
	 * 				положение true указывает, что передается полностью выраженное условие
	 */
	public function delete_images($image, $clwhere=false) {

                global $db, $parse;

		if(is_numeric($image) || is_integer($image))
		        $where = " id='".$image."' ";
		else
			$where = " attachedto='".$image."' ";

		if($clwhere) $where = $image;

                $q = $db->query("SELECT id, filename, fileext FROM ".IMAGES_TABLE." WHERE ".$where);
                while($row = $db->fetch_assoc($q)) {
                	if(!empty($row)) {
                		$original = $row['filename']."_original.".$row['fileext'];
                		$resize = $row['filename']."_resize.".$row['fileext'];
                		$thumb = $row['filename']."_thumb.".$row['fileext'];

                		# delete original
				if(file_exists(_UPLOADIMAGES."/".$original)) {
                                	unlink(_UPLOADIMAGES."/".$original);
                                	if(DEBUGMODE) $parse->msg("Изображение ".$original." удалено");
				}
				elseif(!file_exists(_UPLOADIMAGES."/".$original) && DEBUGMODE) $parse->msg("Не удалось найти изображение ".$original, false);

				# delete resize
				if(file_exists(_UPLOADIMAGES."/".$resize)) {
                                	unlink(_UPLOADIMAGES."/".$resize);
                                	if(DEBUGMODE) $parse->msg("Изображение ".$resize." удалено");
				}
				elseif(!file_exists(_UPLOADIMAGES."/".$resize) && DEBUGMODE) $parse->msg("Не удалось найти изображение ".$resize, false);

				# delete thumb
				if(file_exists(_UPLOADIMAGES."/".$thumb)) {
                                	unlink(_UPLOADIMAGES."/".$thumb);
                                	if(DEBUGMODE) $parse->msg("Изображение ".$thumb." удалено");
				}
				elseif(!file_exists(_UPLOADIMAGES."/".$thumb) && DEBUGMODE) $parse->msg("Не удалось найти изображение ".$thumb, false);
                	}
                }

                $db->query("DELETE FROM ".IMAGES_TABLE." WHERE ".$where);
        }


	/**
	 * Функция проверяет ввод параметров ширины и высоты для генерации уменьшинных изображений.
	 */
	public function check_post_thumb_parametrs() {

		global $POST;

		if(!isset($POST->thumb_img_width) || trim($POST->thumb_img_width) == "") $POST->thumb_img_width = 0;
		if(!isset($POST->thumb_img_height) || trim($POST->thumb_img_height) == "") $POST->thumb_img_height = 0;

		$POST->thumb_img_width = round($POST->thumb_img_width);
		$POST->thumb_img_height = round($POST->thumb_img_height);

		if($POST->thumb_img_width < 16) $POST->thumb_img_width = 0;
		if($POST->thumb_img_height < 16) $POST->thumb_img_height = 0;
	}


	/**
	 * Функция составляет массив допустимых расширений изображений разрешенных для загрузки на сервер.
	 *
	 * @return mixed Возвращает массив с допустимыми расширениями изображения для загрузки на сервер
	 */
	public function get_allow_exts() {
		require _LIB."/mimetype.php";

		foreach($imagetype AS $itype) {
			$allow_exts[$itype['mime_type']] = $itype['ext'];
		}

		return $allow_exts;
	}
}

?>