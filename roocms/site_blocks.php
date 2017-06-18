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
 * @package      RooCMS
 * @subpackage   Frontend
 * @author       alex Roosso
 * @copyright    2010-2015 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.2.1
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################



/**
 * Class Blocks
 */
class Blocks {

	/**
	* Загружаем блок
	*
	* @param int|string $buid - Алиас или идентификатор блока
	* @return text $output  - возвращает код блока
	*/
	public function load($buid) {

		global $db, $parse, $files, $img, $smarty, $tpl;
                static $use_blocks = array();

                $output = "";

		$buid = str_ireplace("'", "", $buid);
                if(!array_key_exists($buid, $use_blocks)) {

			$buid = strtr($buid, array('\''		=> '',
						   '"'		=> '',
						   '&quot;'	=> ''));

			$q = $db->query("SELECT id, alias, content, block_type FROM ".BLOCKS_TABLE." WHERE id='".$buid."' OR alias='".$buid."'");
			$data = $db->fetch_assoc($q);

			if(!empty($data)) {
				if($data['block_type'] == "php") {
					ob_start();
						eval($parse->text->html($data['content']));

						$output = ob_get_contents();
					ob_end_clean();
				}
				else {
					$output = $parse->text->html($data['content']);

					# load attached images
					$images = $img->load_images("blockid=".$data['id']);

					# load attached files
					$attachfile = $files->load_files("blockid=".$data['id']);


					$smarty->assign("attachfile", $attachfile);
					$smarty->assign("images", $images);
					$smarty->assign("block_id", $data['id']);
					$smarty->assign("block_alias", $data['alias']);

					$imgs = $tpl->load_template("block_attached", true);

					$output .= $imgs;
				}

				$use_blocks[$buid] = $output;
			}
			else {
				if(DEBUGMODE) {
					$output = "Блок с ID или ALIAS - \"".$buid."\" не найден";
				}
			}

                }
                else {
                	$output = $use_blocks[$buid];
		}

		return $output;
	}
}


/**
 * Init Class
 */
$blocks = new Blocks;

/**
 * assign in templates
 */
$smarty->assign("blocks", $blocks);

?>