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
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


trait TemplateExtends {

	/**
	 * Load template for upload images
	 * Determines which of the templates you want to connect and in which variable
	 *
	 * @param string $smarty_variable - variable for smarty template.
	 * @param string $tpl             - If you have use specific template
	 * @param bool   $tplreturn       - Return compiled template to variable. Enabled by default.
	 */
	public function load_image_upload_tpl(string $smarty_variable, string $tpl="attached_images_upload", bool $tplreturn=true) {

		global $smarty;

		$imagetype = [];
		require _LIB."/mimetype.php";
		$smarty->assign("allow_images_type", $imagetype);

		$smarty_tpl = $this->load_template("{$tpl}", $tplreturn);
		$smarty->assign("{$smarty_variable}", $smarty_tpl);
	}


	/**
	 * Load template for upload files
	 * Determines which of the templates you want to connect and in which variable
	 *
	 * @param string $smarty_variable - variable for smarty template.
	 * @param string $tpl             - На случай если вам потребуется использовать собственный шаблон
	 * @param bool   $tplreturn       - Return compiled template to variable. Enabled by default.
	 */
	public function load_files_upload_tpl(string $smarty_variable, string $tpl="attached_files_upload", bool $tplreturn=true) {

		global $smarty;

		$filetype = [];
		require _LIB."/mimetype.php";
		$smarty->assign("allow_files_type", $filetype);

		$smarty_tpl = $this->load_template("{$tpl}", $tplreturn);
		$smarty->assign("{$smarty_variable}", $smarty_tpl);
	}

	/**
	 * Abstract
	 *
	 * @param string $tpl    - template name
	 * @param bool   $return - if use $return to true, function return dump data tpl
	 *
	 * @return string|null
	 */
	abstract protected function load_template(string $tpl, bool $return=false);
}
