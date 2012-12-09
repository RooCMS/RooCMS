<?php
/**
* @package		RooCMS
* @subpackage	Frontend
* @subpackage	Blocks
* @author		alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link			http://www.roocms.com
* @version		1.0.4
* @since		$date$
* @license		http://www.gnu.org/licenses/gpl-2.0.html
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################

$blocks = new Blocks;

$smarty->assign("blocks", $blocks);


class Blocks {

	/**
	* Загружаем блок
	*
	* @param int|string $id - Алиас или идентификатор блока
	* @return text $output  - возвращает код блока
	*/
	public function load($id) {

		global $db, $debug, $parse, $structure, $smarty, $tpl;

		$output = "";

		$id = strtr($id, array(	'\''		=> '',
								'"'			=> '',
								'&quot;'	=> ''));

		if($db->check_id($id, BLOCKS_TABLE) || $db->check_id($id, BLOCKS_TABLE, "alias") ) {
			$q = $db->query("SELECT id, alias, content, type FROM ".BLOCKS_TABLE." WHERE id='".$id."' OR alias='".$id."'");
			$data = $db->fetch_assoc($q);

			if($data['type'] == "php") {
				ob_start();
					eval($parse->text->html($data['content']));

					$output = ob_get_contents();
				ob_end_clean();
			}
			else {
				$output = $parse->text->html($data['content']);

				# load attached images
				$images = array();
				$images = $structure->load_images("blockid=".$data['id']);

				$smarty->assign("images", $images);
				$smarty->assign("block_id", $data['id']);
				$smarty->assign("block_alias", $data['alias']);

				$imgs = $tpl->load_template("block_images", true);

				$output .= $imgs;
			}
		}
		else {
			if($debug->debug) {
				$output = "Блока с ID / ALIAS - \"".$id."\" не существует";
			}
		}

		return $output;
	}
}

?>