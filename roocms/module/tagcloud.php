<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
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
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
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
 * @package     RooCMS
 * @subpackage  Module
 * @author      alex Roosso
 * @copyright   2010-2018 (c) RooCMS
 * @link        http://www.roocms.com
 * @version     1.1
 * @since       $date$
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class Module_Auth
 */
class Module_Tag_Cloud {

	public $title = "Облако Тегов";

	# buffer out
	private $out = "";


	/**
	 * Start
	 */
	public function __construct() {

		global $tags, $tpl, $smarty;

		# get tag listen
		$taglist = $tags->list_tags();

		# set min/max
		$min = $taglist[count($taglist)-1]['amount'];
		$max = $taglist[0]['amount'];

		$minsize = 90;
		$maxsize = 175;

		foreach ($taglist AS $key=>$value) {
			if($min == $max) {
				$fontsize = round(($maxsize - $minsize)/2+$minsize);
			}
			else {
				$fontsize = round(((($maxsize-$minsize)/$max)*$value['amount'])+$minsize);
			}

			$ukey = urlencode($value['title']);

			$taglist[$key] = array('title'=>$value['title'], 'amount'=>$value['amount'], 'fontsize'=>$fontsize, 'ukey'=>$ukey);
		}

		shuffle($taglist);

		$smarty->assign("tags", $taglist);
		$this->out .= $tpl->load_template("module_tagcloud", true);

		# finish
		echo $this->out;
	}
}


/**
 * Init class
 */
$module_tagcloud = new Module_Tag_Cloud;

?>