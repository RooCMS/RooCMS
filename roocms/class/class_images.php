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
 * Class Images
 */
class Images extends GD {


	/**
	 * Раздатчик функции загрузки файлов на сервер и в БД
	 *
	 * @param string $file      - имя в массиве $_FILES
	 * @param string $prefix    - префикс для имения файла.
	 * @param array  $thumbsize - array(width,height) - размеры миниатюры будут изменены согласно параметрам.
	 * @param bool   $watermark - флаг указывает наносить ли водяной знак на рисунок.
	 * @param bool   $modify    - флаг указывает подвергать ли изображение полной модификации с сохранением оригинального изображения и созданием превью.
	 * @param bool   $noresize  - флаг указывает подвергать ли изображение изменению размера. Иcпользуется в том случае когда мы не хотим изменять оригинальное изображение.
	 * @param string $fname     - устанавливаем имя для файла принудительно
	 * @param string $path      - путь к папке для загрузки изображений.
	 *
	 * @return false|array - возвращает массив с именами файлов.
	 */
	public function upload_image($file, $prefix="", array $thumbsize=[], $watermark=true, $modify=true, $noresize=false, $fname="", $path=_UPLOADIMAGES) {
		return $this->upload_post_image($file, $prefix, $thumbsize, $watermark, $modify, $noresize, $fname, $path);
	}


	/**
	 * Загрузка картинок через $_POST
	 *
	 * @param string $file      - имя в массиве $_FILES
	 * @param string $prefix    - префикс для имения файла.
	 * @param array  $thumbsize - array(width,height) - размеры миниатюры будут изменены согласно параметрам.
	 * @param bool   $watermark - флаг указывает наносить ли водяной знак на рисунок.
	 * @param bool   $modify    - флаг указывает подвергать ли изображение полной модификации с сохранением оригинального изображения и созданием превью.
	 * @param bool   $noresize  - флаг указывает подвергать ли изображение изменению размера. Иcпользуется в том случае когда мы не хотим изменять оригинальное изображение.
	 * @param string $fname     - устанавливаем имя для файла принудительно
	 * @param string $path      - путь к папке для загрузки изображений.
	 *
	 * @return false|array - возвращает массив с именами файлов или false в случае неудачи.
	 */
	public function upload_post_image($file, $prefix="", array $thumbsize=[], $watermark=true, $modify=true, $noresize=false, $fname="", $path=_UPLOADIMAGES) {

		global $config, $files;

		# Если ложный вызов
		if(!isset($_FILES[$file])) {
			return false;
		}

		# Объявляем выходной массив
		$images = [];

		# Составляем массив для проверки разрешенных типов файлов к загрузке
		static $allow_exts = [];
		if(empty($allow_exts)) {
			$allow_exts = $this->get_allow_images();
		}


		# Определяем настройки размеров для будущих миниатюр
		if(!empty($thumbsize)) {
			$this->set_mod_sizes($thumbsize);
		}


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

				# Грузим апельсины бочками
				if(array_key_exists($upfiles[$file]['type'][$key], $allow_exts)) {

					# расширение файла
					$ext = $allow_exts[$upfiles[$file]['type'][$key]];

					# подменяем, если необходимо имя файла
					if($fname != "") {
						$upfiles[$file]['name'][$key] = $fname.".".$ext;
					}

					# Создаем имя файлу.
					$filename = $files->create_filename($upfiles[$file]['name'][$key], $prefix, "", $path);

					# если разрешено сохранять оригинальное изображение
					if($modify) {
						# Сохраняем оригинал
						copy($upfiles[$file]['tmp_name'][$key], $path."/".$filename."_original.".$ext);

						# Если загрузка прошла и файл на месте
						$upload = is_file($path."/".$filename."_original.".$ext);
					}
					else {
						# Сохраняем оригинал
						copy($upfiles[$file]['tmp_name'][$key], $path."/".$filename.".".$ext);

						# Если загрузка прошла и файл на месте
						$upload = is_file($path."/".$filename.".".$ext);
					}
				}

				# Если загрузка удалась
				if($upload) {
					# convert jpgtowebp
					if($config->gd_convert_jpg_to_webp) {
						$ext = $this->convert_jpgtowebp($filename, $ext, $path);
					}

					$this->modify_image($filename, $ext, $path, $watermark, $modify, $noresize);
				}
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

			if($filename) {
				$images[] = $filename.".".$ext;
			}
		}

		# Возвращаем массив имен файлов для внесения в БД
		return (count($images) > 0) ? $images : false ;
	}


	/**
	 * Выгружаем присоедененные изображения
	 *
	 * @param string $cond - параметр указывающий на элемент к которому прикреплены изображения
	 * @param int    $from - стартовая позиция для загрузки изображений
	 * @param int    $limit - лимит загружаемых изображений
	 *
	 * @return array $data - массив с данными об изображениях.
	 */
	public function load_images($cond, $from = 0, $limit = 0) {

                global $db;

		$data = [];

		$l = ($limit != 0) ? "LIMIT {$from},{$limit}" : "" ;

		$q = $db->query("SELECT id, filename, fileext, sort, alt FROM ".IMAGES_TABLE." WHERE attachedto='{$cond}' ORDER BY sort ASC ".$l);
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

        	global $db, $logger;

		$image = pathinfo($filename);

		$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename, fileext, alt)
						    VALUES ('".$attached."', '".$image['filename']."', '".$image['extension']."', '".$alt."')");

		# msg
		$logger->log("Изображение ".basename($filename)." успешно загружено на сервер");
	}


	/**
	 * Обноялвем информацию об изображениях
	 *
	 * @param mixed $attachedto - структурный определитель
	 * @param int   $id         - идентификатор изображения
	 */
	public function update_images_info($attachedto, $id) {

		global $db, $post, $img, $parse;

		if(isset($post->sort) || isset($post->alt)) {
			$sortimg = $img->load_images($attachedto."=".$id);
			foreach($sortimg AS $v) {

				$cond = "";

				if(isset($post->sort[$v['id']]) && $post->sort[$v['id']] != $v['sort']) {
					$cond .= "sort='".$post->sort[$v['id']]."'";
				}

				if(!isset($post->alt[$v['id']])) {
					$post->alt[$v['id']] = "";
				}

				if($post->alt[$v['id']] != $v['alt']) {
					$cond = $parse->text->comma($cond);
					$cond .= "alt='".$post->alt[$v['id']]."'";
				}

				if($cond != "") {
					$db->query("UPDATE ".IMAGES_TABLE." SET ".$cond." WHERE id='".$v['id']."'");
				}
			}
		}
	}


	/**
	 * Функция удаления картинок
	 *
	 * @param int|string $image - указать числовой идентификатор или attachedto
	 * @param boolean    $clwhere - флаг указывает как считывать параметр $image
	 * 				положение false указывает, что передается параметр id или attachedto
	 * 				положение true указывает, что передается полностью выраженное условие
	 */
	public function remove_images($image, $clwhere=false) {

                global $db;

		if(is_numeric($image) || is_integer($image)) {
			$cond = " id='".$image."' ";
		}
		else {
			$cond = " attachedto='".$image."' ";
		}

		if($clwhere) {
			$cond = $image;
		}

                $q = $db->query("SELECT id, filename, fileext FROM ".IMAGES_TABLE." WHERE ".$cond);
                while($row = $db->fetch_assoc($q)) {
                	if(!empty($row)) {
                		$original = $row['filename']."_original.".$row['fileext'];
                		$resize = $row['filename']."_resize.".$row['fileext'];
                		$thumb = $row['filename']."_thumb.".$row['fileext'];

				# delete unique name
				//$this->erase_image(_UPLOADIMAGES."/".$row['filename'].".".$row['fileext']);

                		# delete original
				$this->erase_image(_UPLOADIMAGES."/".$original);

				# delete resize
				$this->erase_image(_UPLOADIMAGES."/".$resize);

				# delete thumb
				$this->erase_image(_UPLOADIMAGES."/".$thumb);
                	}
                }

                $db->query("DELETE FROM ".IMAGES_TABLE." WHERE ".$cond);
        }


	/**
	 * Функция стирает файл с указанным изображением
	 *
	 * @param $image
	 */
	public function erase_image($image) {

		global $logger;

		if(is_file($image)) {
			unlink($image);
			$logger->log("Изображение ".basename($image)." удалено");
		}
		else {
			$logger->log("Не удалось найти изображение ".basename($image), "error");
		}
	}


	/**
	 * Функция проверяет ввод параметров ширины и высоты для генерации уменьшинных изображений.
	 */
	public function check_post_thumb_parametrs() {

		global $post;

		if(!isset($post->thumb_img_width)) {
			$post->thumb_img_width = 0;
		}
		if(!isset($post->thumb_img_height)) {
			$post->thumb_img_height = 0;
		}

		$post->thumb_img_width = round($post->thumb_img_width);
		$post->thumb_img_height = round($post->thumb_img_height);

		if($post->thumb_img_width < 16) {
			$post->thumb_img_width = 0;
		}
		if($post->thumb_img_height < 16) {
			$post->thumb_img_height = 0;
		}
	}


	/**
	 * Функция составляет массив допустимых расширений изображений разрешенных для загрузки на сервер.
	 *
	 * @return mixed Возвращает массив с допустимыми расширениями изображения для загрузки на сервер
	 */
	public function get_allow_images() {
		$imagetype = [];
		require _LIB."/mimetype.php";

		foreach($imagetype AS $itype) {
			$allow_exts[$itype['mime_type']] = $itype['ext'];
		}

		return $allow_exts;
	}
}