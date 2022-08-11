<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


/**
 * Init RooCMS
 */
define('_SITEROOT', dirname(__FILE__));
require_once _SITEROOT."/roocms/init.php";

# checked captcha
if(isset($roocms->sess['captcha']) && mb_strlen($roocms->sess['captcha']) == 5) {
	$captcha_code = $roocms->sess['captcha'];
}
else {
	$captcha_code  = randcode(5,"123456789ABCEFHKLMNPRSTQVUWXZ");
	$_SESSION['captcha'] = $captcha_code;
}

#renew code
if(isset($get->_I_have_bad_sight)) {
	$captcha_code  = randcode(5,"123456789ABCEFHKLMNPRSTQVUWXZ");
	$_SESSION['captcha'] = $captcha_code;
}

# no cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);

# load aRCaptcha
require_once(_LIB."/captcha.php");

# draw captcha
echo aRCaptcha::show($captcha_code);

# debug palette
//echo aRCaptcha::palette();
