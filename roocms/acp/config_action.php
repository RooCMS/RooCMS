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
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


class ACP_ConfigAction {


	/**
	 * This function is intended to initialize specific functions
	 * that are used for some sections.
	 */
	protected function init_for_special_part() {

		global $post;

		# If changed name script acp
		# Create new file
		if(isset($post->cp_script) && CP != $post->cp_script) {
			# change cp script
			$post->cp_script = $this->change_cp_script($post->cp_script);
		}
	}


	/**
	 * This function to change script name for acp.
	 *
	 * @param string $newcp - new path for acp script
	 *
	 * @return string        - actual path to script acp
	 */
	private function change_cp_script(string $newcp) {

		global $files, $logger;

		# read data
		$context = file_read(_SITEROOT."/".CP);

		# create and write
		if(!file_exists(_SITEROOT."/".$newcp)) {
			# craft new file
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
