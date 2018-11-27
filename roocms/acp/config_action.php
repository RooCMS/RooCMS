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
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


class ACP_ConfigAction {


	/**
	 * Данная функция предназначена для инициализации особых функций,
	 * которые используются для некоторых разделов.
	 */
	protected function init_for_special_part() {

		global $post;

		# Если изменено имя скрипта Панели Администратора.
		# Пробуем создать новый файл.
		if(isset($post->cp_script) && CP != $post->cp_script) {
			# change cp script
			$post->cp_script = $this->change_cp_script($post->cp_script);
		}
	}


	/**
	 * Функция изменения адреса входной страницы в Панель Администратора
	 *
	 * @param string $newcp  - новый путь скрипта панели администратора
	 *
	 * @return string        - новый или действующий путь к скрипту панели администратора
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