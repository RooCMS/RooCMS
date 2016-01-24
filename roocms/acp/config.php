<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Configuration settings
* @author       alex Roosso
* @copyright    2010-2016 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2
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
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


class ACP_CONFIG {

	# classes
	var $config;

	# type config
	private $types	= array();
	private $t_vars	= array();

	private $part	= "global";



	/**
	* Start
	*
	*/
	public function __construct() {

		global $db, $config, $tpl, $POST;


		# include config class
		$this->config = $config;


		# Если есть запрос на обновление тогда обновляем
		if(isset($POST->update_config))	$this->update_config();
		else				$this->view_config();


		# Load Template
		$tpl->load_template("config");
	}


	/**
	* Показать настройки
	*
	*/
	private function view_config() {

		global $db, $tpl, $smarty, $html, $parse, $GET;


		if(isset($GET->_part) && $db->check_id($GET->_part, CONFIG_PARTS, "name") == 1) $this->part = $GET->_part;
		//elseif(isset($GET->_part) && $GET->_part == "all") $this->part = "all";


		# запрос разделов конфигурации из БД
		$q_1 = $db->query("SELECT name, title, type, ico FROM ".CONFIG_PARTS." ORDER BY type ASC, sort ASC");
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

			if($part['type'] == "global")		$parts['global'][] 	= $part;
			if($part['type'] == "component")	$parts['component'][] 	= $part;
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


		# integer OR string OR email
		if($option_type == "int" OR $option_type == "integer" OR $option_type == "string" OR $option_type == "email" OR $option_type == "color") {
			$out = $tpl->load_template("config_field_string",true);
		}
		# text OR textarea
		elseif($option_type == "text" OR $option_type == "textarea") {
			$out = $tpl->load_template("config_field_textarea",true);
		}
		# boolean
		elseif($option_type == "boolean" OR $option_type == "bool") {
			$out = $tpl->load_template("config_field_boolean",true);
		}
		# date
		elseif($option_type == "date") {
			$field['value'] = $parse->date->unix_to_rusint($field['value']);
			$out = $tpl->load_template("config_field_date",true);
		}
		# select
		elseif($option_type == "select" && trim($variants) != "") {
			$vars = explode("\n",$variants);
			foreach($vars AS $k=>$v) {
				$vars = explode("|",trim($v));

				($vars[1] == $value) ? $s = "selected" : $s = "" ;

				$field['variants'][] = array('value'=>$vars[1], 'title'=>$vars[0], 'selected'=>$s);
			}

			// reup
			$smarty->assign('field', $field);

			$out = $tpl->load_template("config_field_select",true);
		}
		# image
		elseif($option_type == "image" OR $option_type == "img") {

			$image = array();

			if(trim($field['value']) != "" && file_exists(_UPLOADIMAGES."/".$field['value'])) {

				$image['src'] = $field['value'];

				$size = getimagesize(_UPLOADIMAGES."/".$image['src']);
				$image['width'] = $size[0];
				$image['height'] = $size[1];
			}

			$smarty->assign("image", $image);

			$out = $tpl->load_template("config_field_image", true);
		}


		return $out;
	}


	/**
	 * Функция обновления настроек
	 */
	private function update_config() {

		global $db, $parse, $POST, $img;

		# запрашиваем из БД типа опций
		$q = $db->query("SELECT option_name, option_type, variants FROM ".CONFIG_TABLE);
		while($row = $db->fetch_assoc($q)) {

			$this->types[$row['option_name']] = $row['option_type'];

			if(trim($row['variants']) != "") {

				$vars = explode("\n",$row['variants']);

				foreach($vars AS $k=>$v) {
					$v = explode("|",trim($v));
					$this->t_vars[$row['option_name']][$v[1]] = trim($v[1]);
				}
			}
		}


		# Если изменено имя скрипта Панели Администратора.
		# Пробуем создать новый файл.
		if(isset($POST->cp_script) && CP != $POST->cp_script) {
			if($this->change_cp_script($POST->cp_script)) {
				$gonewcp = $POST->cp_script;
			}
			else $POST->cp_script = CP;
		}


		# Обновляем опции
		foreach($POST AS $key=>$value) {
			if($key != "update_config") {
				$check = false;

				# int OR integer
				if($this->types[$key] == "int" OR $this->types[$key] == "integer") {
					if(is_numeric($value)) {
						settype($value, "integer");
						$check = true;
					}
					else $check = false;
				}
				# boolean OR bool
				elseif($this->types[$key] == "boolean" OR $this->types[$key] == "bool") {
					if($value == "true" OR $value == "false") {
						$check = true;
					}
					else $check = false;
				}
				# email
				elseif($this->types[$key] == "email") {
					if($parse->valid_email($value))
						$check = true;
					else
						$check = false;
				}
				# date
				# EDIT THIS CODE!!!!!!!!!!!!!!!!!!!!!
				elseif($this->types[$key] == "date") {
					$date = explode(".",$POST->$key);
					$d = settype($date[0], "integer");
					$m = settype($date[1], "integer");
					$y = settype($date[2], "integer");

					if(count($date) == 3) {
						if(mb_strlen($date[0], 'utf8') <= 2 && mb_strlen($date[1], 'utf8') <= 2 && mb_strlen($date[2], 'utf8') == 4 && checkdate($m, $d, $y)) {
							$check = true;
							$value = $parse->date->gregorian_to_unix($m."/".$d."/".$y);
						}
						else $check = false;
					}
					else $check = false;
				}
				# string OR text
				elseif($this->types[$key] == "string" OR $this->types[$key] == "text" OR $this->types[$key] == "textarea" OR $this->types[$key] == "color") {
					$check = true;
				}
				# select
				elseif($this->types[$key] == "select") {
					if(isset($this->t_vars[$key][$value])) $check = true;
				}
				# image
				elseif($this->types[$key] == "image" OR $this->types[$key] == "img") {

					$image = $img->upload_image("image_".$key, "", array(), array("filename"=>$key, "watermark"=>false, "modify"=>false, "noresize"=>true));

					if(isset($image[0])) {
						if($value != "" && $value != $image[0]) unlink(_UPLOADIMAGES."/".$value);

						$value = $image[0];

						$check = true;
					}
				}


				if($check) {
					$db->query("UPDATE ".CONFIG_TABLE." SET value='".$value."' WHERE option_name='".$key."'");
				}
			}
		}


		# уведомление
		$parse->msg("Настройки обновлены");

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
	 * Функция изменения адреса входной страницы в Панель Администратора
	 *
	 * @param $newcp - новый путь скрипта панели администратора
	 *
	 * @return bool - флаг успеха/провала
	 */
	private function change_cp_script($newcp) {

		global $parse;

		$nowcp = file(_SITEROOT."/".CP);

		# Собираем лут из старого файла
		$context = "";
		for($i=0;$i<=count($nowcp)-1;$i++) {
			$context .= $nowcp[$i];
		}

		# Создаем и записываем
		if(!file_exists(_SITEROOT."/".$newcp)) {
			# крафтим новый файл
			$cps = fopen($newcp, "w+");
			if(is_writable($newcp)) {
				fwrite($cps, $context);
			}
			fclose($cps);

			if(file_exists(_SITEROOT."/".$newcp)) {
				if(DEBUGMODE) $parse->msg("Новый файл для входа в панель управления успешно создан!");
				return true;
			}
			else {
				$parse->msg("Не удалось создать новый файл для входа в панель управления! Проверьте chmod настройки на сервере для работы с файлами.", false);
				return false;
			}

		} else {
			$parse->msg("У вас уже есть такой файл. Новое имя скрипта панели управление не должно совпадать с уже имеющимся файлом. Укажите другое имя для создаваемого файла.", false);
			return false;
		}
	}
}

/**
 * Init Class
 */
$acp_config = new ACP_CONFIG;

?>