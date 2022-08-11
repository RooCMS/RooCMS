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
if(!defined('RooCMS') || !defined('INSTALL')) {
	die('Access Denied');
}
//#########################################################


class Update extends IU_Extends {

	# vars
	protected $allowed	= true;		# [bool]	flag for allowed to continue process
	protected $log		= [];		# [array]	array log process actions

	private $action		= "update";	# [string]	alias for identy process
	protected $step		= 1;		# [int]		now use step
	protected $nextstep	= 2;		# [int]		next use step
	protected $steps	= 4;		# [int]		all step in operations
	protected $page_title	= "";
	protected $status	= "";
	protected $noticetext	= "";		# [string]	attention text in head form



	/**
	 * Вперед...
	 */
	public function __construct() {

		global $site, $parse, $tpl, $smarty;

		# init step
		$this->init_step();

		# seo
		$site['title'] = "Обновление RooCMS";

		# go
		switch($this->step) {

			case 2:
				$this->step_2();
				break;

			case 3:
				$this->page_title = "Обновление RooCMS";
				$this->status = "Обновление RooCMS<br />...";
				$this->step_3();
				break;

			default:
				$this->step_1();
				break;
		}

		# nextstep
		$this->set_nextstep();

		# draw
		$smarty->assign("allowed",	$this->allowed);
		$smarty->assign("action",	$this->action);
		$smarty->assign("page_title", 	$this->page_title);
		$smarty->assign("status", 	$this->status);
		$smarty->assign("step", 	$this->step);
		$smarty->assign("nextstep", 	$this->nextstep);
		$smarty->assign("steps",	$this->steps);
		$smarty->assign("progress",	$parse->percent($this->step, $this->steps));

		$tpl->load_template("top");

		$smarty->assign("log", 		$this->log);
		$smarty->assign("noticetext", 	$this->noticetext);

		$tpl->load_template("body");
	}


	/**
	 * Sorry
	 */
	private function step_3() {
		$this->log[] = array('', 'Извините.<br />Сегодня автоматические обновление невозможно.<br />В ближайшем будущем мы неприменно его реализуем, но сейчас, любое обновление придется делать вручную.', false, '');
		$this->log[] = array('', 'Ваша версия RooCMS: '.ROOCMS_VERSION.'', true, '');

		if(get_http_response_code("http://version.roocms.com/index.php") == "200") {
			$f = file("http://version.roocms.com/index.php");
			if(!empty($f)) {
				$this->log[] = array('', 'Последняя версия: '.$f[0].'', true, '');
			}
		}
	}
}