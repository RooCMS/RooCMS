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

/**
 * @package      RooCMS
 * @subpackage	 Admin Control Panel
 * @subpackage	 Configuration settings
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


class ACP_Config_SpecPart {


	/**
	 * Данная функция предназначена для инициализации особых функций,
	 * которые используются для некоторых разделов.
	 */
	protected function init_for_special_part() {

		global $post;

		# Если изменено имя скрипта Панели Администратора.
		# Пробуем создать новый файл.
		if(isset($post->cp_script) && CP != $post->cp_script) {
			$post->cp_script = $this->change_cp_script($post->cp_script);
		}
	}


	/**
	 * Функция изменения адреса входной страницы в Панель Администратора
	 *
	 * @param $newcp  - новый путь скрипта панели администратора
	 *
	 * @return bool   - флаг успеха/провала
	 */
	private function change_cp_script($newcp) {

		global $files, $logger;

		# Собираем лут из старого файла
		$context = file_read(_SITEROOT."/".CP);

		# Создаем и записываем
		if(!file_exists(_SITEROOT."/".$newcp)) {
			# крафтим новый файл
			$files->write_file($newcp, $context);

			if(file_exists(_SITEROOT."/".$newcp)) {
				$logger->info("Новый файл для входа в панель управления успешно создан!");
				return $newcp;
			}
			else {
				$logger->error("Не удалось создать новый файл для входа в панель управления! Проверьте chmod настройки на сервере для работы с файлами.");
				return CP;
			}

		}
		else {
			$logger->error("У вас уже есть такой файл. Новое имя скрипта панели управление не должно совпадать с уже имеющимся файлом. Укажите другое имя для создаваемого файла.");
			return CP;
		}
	}
}