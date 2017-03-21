<?php
/**
 *   RooCMS - Russian free content managment system
 *   Copyright © 2010-2017 alexandr Belov aka alex Roosso. All rights reserved.
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
 *   RooCMS - Русская бесплатная система управления контентом
 *   Copyright © 2010-2017 александр Белов (alex Roosso). Все права защищены.
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
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Configuration settings
* @author       alex Roosso
* @copyright    2010-2017 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.3.1
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


class ACP_CONFIG {

	# classes
	var $config;

	private $part	= "global";



	/**
	* Start
	*
	*/
	public function __construct() {

		global $config, $tpl, $POST;


		# include config class
		$this->config = $config;

		# Если есть запрос на обновление тогда обновляем
		if(isset($POST->update_config))	{
			$this->update_config();
		}
		else {
			$this->view_config();
		}

		# Load Template
		$tpl->load_template("config");
	}


	/**
	* Показать настройки
	*
	*/
	private function view_config() {

		global $db, $smarty, $GET;

		$parts = array();

		if(isset($GET->_part) && $db->check_id($GET->_part, CONFIG_PARTS_TABLE, "name") == 1) {
			$this->part = $GET->_part;
		}
		//elseif(isset($GET->_part) && $GET->_part == "all") $this->part = "all";

		# запрос разделов конфигурации из БД
		$q_1 = $db->query("SELECT name, title, type, ico FROM ".CONFIG_PARTS_TABLE." ORDER BY type ASC, sort ASC");
		while($part = $db->fetch_assoc($q_1)) {

			# запрашиваем из БД опции
			if($this->part == $part['name']) {

				$this_part = $part;

				$q_2 = $db->query("SELECT id, title, description, option_name, option_type, variants, value, default_value, field_maxleight FROM ".CONFIG_TABLE." WHERE part='".$part['name']."' ORDER BY sort ASC");
				while($option = $db->fetch_assoc($q_2)) {

					# parse
					$option['option'] = $this->init_field($option['option_name'], $option['option_type'], $option['value'], $option['variants'], $option['field_maxleight']);

					# compile for output
					$this_part['options'][] = $option;
				}
			}

			if($part['type'] == "global") {
				$parts['global'][] 	= $part;
			}
			if($part['type'] == "component") {
				$parts['component'][] 	= $part;
			}
		}

		$smarty->assign('this_part', 	$this_part);
		$smarty->assign('parts',	$parts);
		$smarty->assign('thispart',	$this->part);
	}


	/**
	 * функция парсера опции для буфера
	 *
	 * @param string $option_name - имя поля
	 * @param string $option_type - тип поля
	 * @param string $value       - значение
	 * @param text   $variants    - варианты (для селектов)
	 *
	 * @param int    $maxlength   - максимально допустимое количество символов в поле.
	 *
	 * @return string - подстановка в шаблон конфигуратора
	 */
	private function init_field($option_name, $option_type, $value, $variants, $maxlength=0) {

		global $tpl, $smarty, $parse;

		$field = array('name'=>$option_name, 'value'=>$value, 'type'=>$option_type, 'maxlength'=>$maxlength);
		$smarty->assign('field', $field);

		switch($option_type) {
			# integer OR string OR email
			case 'int':
			case 'integer':
			case 'string':
			case 'email':
			case 'color':
				$out = $tpl->load_template("config_field_string",true);
				break;

			# text OR textarea
			case 'text':
			case 'textarea':
				$out = $tpl->load_template("config_field_textarea",true);
				break;

			# boolean
			case 'boolean':
			case 'bool':
				$out = $tpl->load_template("config_field_boolean",true);
				break;

			# date
			case 'date':
				$field['value'] = $parse->date->unix_to_rusint($field['value']);
				$out = $tpl->load_template("config_field_date",true);
				break;

			case 'select':
				$vars = explode("\n",$variants);
				foreach($vars AS $v) {
					$vars = explode("|",trim($v));

					($vars[1] == $value) ? $s = "selected" : $s = "" ;

					$field['variants'][] = array('value'=>$vars[1], 'title'=>$vars[0], 'selected'=>$s);
				}

				$smarty->assign('field', $field);
				$out = $tpl->load_template("config_field_select",true);
				break;

			case 'image':
			case 'img':
				$image = array();
				if(trim($field['value']) != "" && file_exists(_UPLOADIMAGES."/".$field['value'])) {
					$image['src'] = $field['value'];
					$size = getimagesize(_UPLOADIMAGES."/".$image['src']);
					$image['width'] = $size[0];
					$image['height'] = $size[1];
				}

				$smarty->assign("image", $image);
				$out = $tpl->load_template("config_field_image", true);
				break;

			default:
				$out = "Нераспознанный параметр";
				break;
		}

		return $out;
	}


	/**
	 * Функция обновления настроек
	 */
	private function update_config() {

		global $db, $parse, $logger, $POST, $img;

		# запрашиваем из БД типы опций и ограничений
		$cfg_vars = array();
		$q = $db->query("SELECT option_name, option_type, variants, field_maxleight FROM ".CONFIG_TABLE);
		while($row = $db->fetch_assoc($q)) {

			$cfg_vars[$row['option_name']]['type'] 		= $row['option_type'];
			$cfg_vars[$row['option_name']]['maxleight'] 	= $row['field_maxleight'];

			if(trim($row['variants']) != "") {

				$vars = explode("\n",$row['variants']);

				foreach($vars AS $v) {
					$v = explode("|",trim($v));
					$cfg_vars[$row['option_name']]['var'][$v[1]] = trim($v[1]);
				}
			}
		}


		# Если изменено имя скрипта Панели Администратора.
		# Пробуем создать новый файл.
		if(isset($POST->cp_script) && CP != $POST->cp_script) {
			if($this->change_cp_script($POST->cp_script)) {
				$gonewcp = $POST->cp_script;
			}
			else {
				$POST->cp_script = CP;
			}
		}

		# Удаляем батон "Сохранить настроки"
		unset($POST->empty);

		# Обновляем опции
		foreach($POST AS $key=>$value) {
			if($key != "update_config") {
				$check = false;

				switch($cfg_vars[$key]['type']) {
					# integer
					case 'int':
					case 'integer':
						$value = round($value);
						settype($value, "integer");
						$check = true;
						break;

					# email
					case 'email':
						$check = $parse->valid_email($value);
						break;

					# text OR textarea
					case 'string':
					case 'color':
					case 'text':
					case 'textarea':
						$value = $this->check_string_value($value,$cfg_vars[$key]['maxleight']);
						$check = true;
						break;

					# boolean
					case 'boolean':
					case 'bool':
						if($value == "true" || $value == "false") {
							$check = true;
						}
						break;

					# date
					case 'date':
						$value = $parse->date->rusint_to_unix($POST->$key);
						$check = true;
						break;

					case 'select':
						if(isset($cfg_vars[$key]['var'][$value])) {
							$check = true;
						}
						break;

					case 'image':
					case 'img':
						$image = $img->upload_image("image_".$key, "", array(), array("filename"=>$key, "watermark"=>false, "modify"=>false, "noresize"=>true));

						if(isset($image[0])) {
							if($value != "" && $value != $image[0]) {
								unlink(_UPLOADIMAGES."/".$value);
							}
							$value = $image[0];
							$check = true;
						}
						break;

					default:
						$check = false;
						break;
				}


				if($check) {
					$db->query("UPDATE ".CONFIG_TABLE." SET value='".$value."' WHERE option_name='".$key."'");
				}
			}
		}


		# уведомление
		$logger->info("Настройки обновлены");

		# move
		if(isset($gonewcp)) { // Если мы изменяли путь скрипта к панели управления.
			$path = getenv("HTTP_REFERER");
			$path = str_ireplace(CP, $gonewcp, $path);

			unlink(_SITEROOT."/".CP);

			go($path);
		}
		else goback();
	}


	/**
	 * Функция обработки строковых данных конфигуратора сайта
	 *
	 * @param string $value 	- Значение
	 * @param int    $maxleight	- Максимальная длина строки
	 *
	 * @return string
	 */
	private function check_string_value($value, $maxleight=0) {

		if($maxleight > 0) {
			$value = substr($value, 0, $maxleight);
		}

		return $value;
	}


	/**
	 * Функция изменения адреса входной страницы в Панель Администратора
	 *
	 * @param $newcp - новый путь скрипта панели администратора
	 *
	 * @return bool - флаг успеха/провала
	 */
	private function change_cp_script($newcp) {

		global $logger;

		# Собираем лут из старого файла
		$context = file_read(_SITEROOT."/".CP);

		# Создаем и записываем
		if(!file_exists(_SITEROOT."/".$newcp)) {
			# крафтим новый файл
			$cps = fopen($newcp, "w+");
			if(is_writable($newcp)) {
				fwrite($cps, $context);
			}
			fclose($cps);

			if(file_exists(_SITEROOT."/".$newcp)) {
				$logger->info("Новый файл для входа в панель управления успешно создан!");
				return true;
			}
			else {
				$logger->error("Не удалось создать новый файл для входа в панель управления! Проверьте chmod настройки на сервере для работы с файлами.");
				return false;
			}

		} else {
			$logger->error("У вас уже есть такой файл. Новое имя скрипта панели управление не должно совпадать с уже имеющимся файлом. Укажите другое имя для создаваемого файла.");
			return false;
		}
	}
}

/**
 * Init Class
 */
$acp_config = new ACP_CONFIG;

?>