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


class IU_Extends extends Requirement {

	# var
	protected $step;
	protected $nextstep;

	protected $page_title;
	protected $status;
	protected $noticetext;


	/**
	 * Step 1 : Licence
	 */
	protected function step_1() {

		$this->page_title = "Лицензионное соглашение";
		$this->status = "Внимательно прочитайте лицензионное соглашение<br />Помните, что нарушение авторских прав влечет за собой уголовную ответсвенность.";

		require_once _LIB."/license.php";

		$this->noticetext = $license['ru'];

		if($this->check_submit()) {
			if($this->check_step(1)) {
				go(SCRIPT_NAME."?step=2");
			}
			else {
				goback();
			}
		}
	}


	/**
	 * Step 2 : Check requirement
	 */
	protected function step_2() {

		$this->page_title = "Проверка требований RooCMS к хостингу";
		$this->status = "Проверяем версию PHP, MySQL, Apache<br />Проверяем наличие требуемых PHP и Apache расширений";

		$this->check_requirement();

		if($this->check_submit()) {
			if($this->check_step(2)) {
				go(SCRIPT_NAME."?step=3");
			}
			else {
				goback();
			}
		}
	}


	/**
	 * Init var $this->>step
	 */
	protected function init_step() {

		global $get;

		if(isset($get->_step) && round($get->_step) > 0) {
			$this->step =& $get->_step;
		}
	}


	/**
	 * This function check steps in algorythm and set next step
	 */
	protected function set_nextstep() {
		if($this->allowed && $this->step != $this->steps) {
			$this->nextstep = $this->step + 1;
		}
	}


	/**
	 * Check data $post->step
	 *
	 * @param int $n - step
	 *
	 * @return bool
	 */
	protected function check_step(int $n) {

		global $post;

		return isset($post->step) && $post->step == $n;
	}


	/**
	 * Check used $post->submit
	 *
	 * @return bool
	 */
	protected function check_submit() {

		global $post;

		return $this->allowed && isset($post->submit);
	}
}