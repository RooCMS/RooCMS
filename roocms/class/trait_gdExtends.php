<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved
 * Contacts: <info@roocms.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 * RooCMS - Бесплатная система управления сайтом с открытым исходным кодом
 * Copyright © 2010-2018 александр Белов  (alex Roosso). Все права защищены
 * Для связи: info@roocms.com
 *
 * Это программа является свободным программным обеспечением. Вы можете
 * распространять и/или модифицировать её согласно условиям Стандартной
 * Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 * Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 * Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 * ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 * ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 * Общественную Лицензию GNU для получения дополнительной информации.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
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


	/**
	 * Check extension on gif or png
	 *
	 * @param string $ext - extension
	 *
	 * @return bool
	 */
	protected function is_gifpng($ext) {

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
	protected function is_jpg($ext) {

		$check = false;

		if($ext == "jpg" || $ext == "jpeg") {
			$check = true;
		}

		return $check;
	}


	/**
	 * Получаем ориентацию изображения
	 *
	 * @param string $image - указываем путь к изображению
	 *
	 * @return int
	 */
	protected function get_orientation($image) {

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
	protected function calc_resize($width, $height, $towidth, $toheight, $resize = true) {

		$x_ratio 	= $towidth / $width;
		$y_ratio 	= $toheight / $height;
		$ratio 		= ($resize) ? min($x_ratio, $y_ratio) : max($x_ratio, $y_ratio);
		$use_x_ratio 	= ($x_ratio == $ratio);
		$new_width 	= $use_x_ratio 	? $towidth : floor($width * $ratio);
		$new_height 	= !$use_x_ratio ? $toheight : floor($height * $ratio);
		$new_left 	= $use_x_ratio 	? 0 : floor(($towidth - $new_width) / 2);
		$new_top 	= !$use_x_ratio ? 0 : floor(($toheight - $new_height) / 2);

		$return = array('new_width'	=> (int) $new_width,
				'new_height'	=> (int) $new_height,
				'new_left'	=> (int) $new_left,
				'new_top'	=> (int) $new_top);

		return $return;
	}


	/**
	 * Корректируем массив с обновленными размерами.
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
	 * @param       $target
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