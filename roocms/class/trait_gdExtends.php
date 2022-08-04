<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
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
 * Class GD_ext
 */
trait GDExtends {

	public $msize = array('w' => 1200,'h' => 1200);	# Max sizes saved image
	public $tsize = array('w' => 267, 'h' => 150);	# Thumbnail defauly sizes


	/**
	 * Check extension on gif or png
	 *
	 * @param string $ext - extension
	 *
	 * @return bool
	 */
	protected function is_gifpng(string $ext) {

		$check = false;

		if($ext == "gif" || $ext == "png") {
			$check = true;
		}

		return $check;
	}


	/**
	 * Check extension on jpg or jpeg
	 *
	 * @param string $ext - extension
	 *
	 * @return bool
	 */
	protected function is_jpg(string $ext) {

		$check = false;

		if($ext == "jpg" || $ext == "jpeg") {
			$check = true;
		}

		return $check;
	}


	/**
	 * Get image orintation
	 *
	 * @param string $image - path to image
	 *
	 * @return int
	 */
	protected function get_orientation(string $image) {

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
	protected function calc_resize(int $width, int $height, int $towidth, int $toheight, bool $resize = true) {

		$x_ratio 	= $towidth / $width;
		$y_ratio 	= $toheight / $height;
		$ratio 		= ($resize) ? min($x_ratio, $y_ratio) : max($x_ratio, $y_ratio);
		$use_x_ratio 	= ($x_ratio == $ratio);
		$new_width 	= $use_x_ratio 	? $towidth : floor($width * $ratio);
		$new_height 	= !$use_x_ratio ? $toheight : floor($height * $ratio);
		$new_left 	= $use_x_ratio 	? 0 : floor(($towidth - $new_width) / 2);
		$new_top 	= !$use_x_ratio ? 0 : floor(($toheight - $new_height) / 2);

		return array('new_width'  => (int) $new_width,
			     'new_height' => (int) $new_height,
			     'new_left'   => (int) $new_left,
			     'new_top'    => (int) $new_top);
	}


	/**
	 * Calculate new size
	 *
	 * @param array $ns - array new size
	 *
	 * @return array $ns
	 */
	protected function calc_newsize(array $ns) {

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
	 * @param array $sizes  - array(width,height) - размеры будут изменены согласно параметрам.
	 * @param mixed $target
	 */
	protected function set_mod_sizes(array $sizes, $target="tsize") {

		if(is_array($sizes) && count($sizes) == 2) {

			$size = [];

			if(round($sizes[0]) > 16) {
				$size['w'] = round($sizes[0]);
			}

			if(round($sizes[1]) > 16) {
				$size['h'] = round($sizes[1]);
			}

			if(!empty($size)) {
				if($target != "tsize") {
					$this->msize = $size;
				}
				else {
					$this->tsize = $size;
				}
			}
		}
	}
}
