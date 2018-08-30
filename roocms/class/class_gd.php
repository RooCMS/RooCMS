<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
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
 * Class GD
 */
class GD extends GDExtends {

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
			$this->copyright = $parse->text->html($site['title']);
			if(trim($config->gd_watermark_string_one) != "") {
				$this->copyright = $parse->text->html($config->gd_watermark_string_one);
			}

			# watermark text string two
			$this->domain = $_SERVER['SERVER_NAME'];
			if(trim($config->gd_watermark_string_two) != "") {
				$this->domain = $parse->text->html($config->gd_watermark_string_two);
			}
		}
	}


	/**
	 * Функция проводит стандартные операции над загруженным файлом.
	 * Изменяет размеры, создает миниатюру, наносит водяной знак.
	 *
	 * @param string $filename  - Имя файла (без расширения)
	 * @param string $extension - расширение файла
	 * @param string $path      - путь к расположению файла.
	 * @param array  $options
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
	 * @param string $filename - Имя файла изображения
	 * @param string $ext      - Расширение файла без точки
	 * @param string $path     - Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
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

	        	$alpha 		= ($this->is_gifpng($ext)) ? 127 : 0 ;
	        	$bgcolor 	= imagecolorallocatealpha($resize, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

	        	# alpha
			if($this->is_gifpng($ext)) {
				imagecolortransparent($resize, $bgcolor);
			}

			imagefilledrectangle($resize, 0, 0, $ns['new_width']-1, $ns['new_height']-1, $bgcolor);

			# вводим в память файл для издевательств
			$src = $this->imgcreate($path."/".$fileoriginal, $ext);

            		imagecopyresampled($resize, $src, 0, 0, 0, 0, $ns['new_width'], $ns['new_height'], $w, $h);

			# льем измененное изображение
			$this->imgsave($resize, $path."/".$fileresize, $ext, $this->rs_quality);

			imagedestroy($resize);
			imagedestroy($src);
		}
	}


	/**
	 * Изменяем размер изображения.
	 *
	 * @param string $filename - Имя файла изображения
	 * @param string $ext      - Расширение файла без точки
	 * @param string $path     - Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
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
		$alpha   = ($this->is_gifpng($ext)) ? 127 : 0;

		$bgcolor = imagecolorallocatealpha($resize, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

		# alpha
		if($this->is_gifpng($ext)) {
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
		$this->imgsave($resize, $path."/".$file, $ext, $this->th_quality);

		imagedestroy($resize);
		imagedestroy($src);

	}


	/**
	 * Генерируем миниатюру изображения для предпросмотра.
	 *
	 * @param string $filename - Имя файла изображения
	 * @param string $ext	   - Расширение файла без точки
	 * @param string $path	   - Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	 */
	protected function thumbnail($filename, $ext, $path=_UPLOADIMAGES) {

		# vars
        	$fileresize 	= $filename."_resize.".$ext;
        	$filethumb 	= $filename."_thumb.".$ext;

		# определяем размер картинки
		$size 		= getimagesize($path."/".$fileresize);

		# вносим в память пустую превью и оригинальный файл, для дальнейшего издевательства над ними.
		$thumb		= $this->imgcreatetruecolor($this->tsize['w'], $this->tsize['h'], $ext);

		$alpha 		= ($this->is_gifpng($ext)) ? 127 : 0 ;
        	$bgcolor	= imagecolorallocatealpha($thumb, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

		# alpha
		if($this->is_gifpng($ext)) {
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
		$this->imgsave($thumb, $path."/".$filethumb, $ext, $this->th_quality);

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
		$shadow = imagecolorallocatealpha($src, 0, 0, 0, 20);
		$color  = imagecolorallocatealpha($src, 255, 255, 255, 20);

		# размер шрифта
		$fontsize = 10;

		# выбираем шрифт
		$fontfile = ""._ROOCMS."/fonts/trebuc.ttf";

		if(trim($this->copyright) != "") {
			imagettftext($src, $fontsize, $angle, 7+1, $size[1]-18+1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $fontsize, $angle, 7-1, $size[1]-18-1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $fontsize, $angle, 7+1, $size[1]-18-1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $fontsize, $angle, 7-1, $size[1]-18+1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $fontsize, $angle, 7, $size[1]-18, $color, $fontfile, $this->copyright);
		}

		if(trim($this->domain) != "") {
			imagettftext($src, $fontsize, $angle, 7+1, $size[1]-5+1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $fontsize, $angle, 7-1, $size[1]-5-1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $fontsize, $angle, 7+1, $size[1]-5-1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $fontsize, $angle, 7-1, $size[1]-5+1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $fontsize, $angle, 7, $size[1]-5, $color, $fontfile, $this->domain);
		}

		# вливаем с ватермарком
		$this->imgsave($src, $path."/".$fileresize, $ext, $this->rs_quality);

        	imagedestroy($src);
	}


	/**
	 * Функция генерация водяного знака на изображении
	 *
	 * @param string $filename - Имя файла
	 * @param string $ext      - Расширение файла без точки
	 * @param string $path     - Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
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
		$this->imgsave($src, $path."/".$fileresize, $ext,  $this->rs_quality);

		imagedestroy($src);
		imagedestroy($watermark);
	}


	/**
	 * Фуекция конвертирует изображение jpg в webp
	 *
	 * @param string $filename - Имя файла
	 * @param string $ext      - Расширение файла без точки
	 * @param string $path     - Путь к папке с файлом. По умолчанию указан путь к папке с изображениями
	 *
	 * @return string          - возвращает новое или неизменное расширение.
	 */
	protected function convert_jpgtowebp($filename, $ext, $path=_UPLOADIMAGES) {

		global $config;

		if($this->is_jpg($ext)) {

			if(is_file($path."/".$filename."_original.".$ext)) {
				$filename = $filename."_original";
			}

			# create
			$src = $this->imgcreate($path."/".$filename.".".$ext,$ext);

			# remove original
			unlink($path."/".$filename.".".$ext);

			# re:set ext for callback
			$ext = "webp";

			# save
			imagewebp($src,$path."/".$filename.".".$ext, $this->rs_quality);

			# destroy
			imagedestroy($src);
		}

		return $ext;
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

			case 'webp':
				$src = imagecreatefromwebp($from);
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

		if($this->is_gifpng($ext)) {
	                imagealphablending($src, false);
			imagesavealpha($src,true);
		}

		return $src;
	}


	/**
	 * Image save
	 *
	 * @param resource $res     - image
	 * @param string   $path    - path for save
	 * @param string   $ext     - extension
	 * @param int      $quality - качество
	 */
	private function imgsave($res, $path, $ext, $quality=0) {
		switch($ext) {
			case 'jpg':
				imagejpeg($res, $path, $quality);
				break;

			case 'webp':
				imagewebp($res, $path, $quality);
				break;

			case 'gif':
				imagegif($res, $path);
				break;

			case 'png':
				imagepng($res, $path);
				break;
		}
	}
}