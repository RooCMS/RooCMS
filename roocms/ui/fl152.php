<?php
/**
 * RooCMS - Russian free content managment system
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
 * RooCMS - Русская бесплатная система управления сайтом
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
 * @subpackage	 Federal law 152
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.0
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('UI')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class UI_FL152
 */
class UI_FL152 {

	public function __construct() {

		global $structure, $config;

		# title
		$structure->page_title = "Соглашение об условиях передачи информации";

		# breadcumb
		$structure->breadcumb[] = array('part'=>'fl152', 'title'=>'Соглашение об условиях передачи информации');

		# goout
		if(!$config->fl152_use) {
			go(SCRIPT_NAME."?part=ucp&act=ucp");
		}

		# show agreement fl 152
		$this->fl152();
	}


	/**
	 * Функция выводит пользовательское соглашение о передачи информации согласно ФЗ РФ 152
	 */
	public function fl152() {

		global $config, $parse, $smarty, $tpl;

		# parse
		$agreement = $parse->text->html($config->fl152_agreement);

		# tpl
		$smarty->assign("agreement", $agreement);
		$tpl->load_template("fl152");
	}
}

/**
 * Init Class
 */
$uifl152 = new UI_FL152;