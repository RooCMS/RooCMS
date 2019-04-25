<?php
/**
* aR Captcha - Protect Form v3
* @copyright © 2007-2019 alexandr Belov aka alex Roosso.
* @author    alex Roosso <info@roocms.com>
* @link      http://www.roocms.com
* @license   MIT
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
* FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
* COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
* IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
* CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/


class aRCaptcha {

	# code
	private static $code = "000000";

	# options for string
	private static $use_number       = true;
	private static $use_upper_letter = true;
	private static $use_lower_letter = false;

	# options for images

	# bg color
	private static $bgcolor = array(254, 254, 254);

	# sizes
	private static $width   = 180;
	private static $height  = 60;

	# allows
	private static $use_circles  = true;
	private static $use_polygons = true;


	# fonts path
	private static $font_path = "fonts";



	public static function show($code="000000") {

		self::set_code($code);

		# get
		$captcha = self::captcha();

		# draw
		header("Content-type: image/jpeg");
		imagejpeg($captcha);
		imagedestroy($captcha);
	}


	/**
	 * CRAFT CAPTCHA
	 *
	 * @return false|mixed|resource
	 */
	private static function captcha() {

		$captcha = imagecreatetruecolor(self::$width, self::$height);
		$bg = imagecolorallocate($captcha, self::$bgcolor[0], self::$bgcolor[1], self::$bgcolor[2]);
		imagefill($captcha, 0, 0, $bg);


		# NOISE
		if(self::$use_polygons) {
			$captcha = self::polygons($captcha);
		}

		if(self::$use_circles) {
			$captcha = self::circles($captcha);
		}


		# letters (use system font)
		$captcha = self::eletters($captcha);


		return $captcha;
	}


	/**
	 * Set code string on image
	 *
	 * @param $captcha - resource image
	 *
	 * @return mixed
	 */
	private static function eletters($captcha) {

		$n = mb_strlen(self::$code);
		$letter_width = (self::$width / $n > self::$height) ? self::$height : self::$width / $n ;

		$xs = 2;
		for($l=0;$l<=$n-1;$l++) {
			$letter_img = imagecreate($letter_width, self::$height);
			$color = imagecolorallocate($letter_img, 255, 255, 255);
			imagefilledrectangle($letter_img, 0, 0, $letter_width, self::$height, $color);
			imagecolortransparent($letter_img, $color);
			$r = mt_rand(0,120);
			$g = mt_rand(0,120);
			$b = mt_rand(0,120);
			$color2 = imagecolorallocatealpha($letter_img, $r, $g, $b, mt_rand(50,65));

			# craft scream
			$radius = mt_rand(2,4);
			$x = mt_rand(1,10);
			$y = mt_rand(1,20);
			imagefilledellipse($letter_img, $x, $y, $radius, $radius, $color2);

			# craft letter
			$letter = substr(self::$code, $l, 1);
			imagestring($letter_img, 5, 0, 0, $letter, $color2);

			# get font size
			$f_w = imagefontwidth(5);
			$f_h = imagefontheight(5);

			# set coords
			$dst_x = mt_rand($xs-3, $xs+3);
			$dst_y = mt_rand(0-$f_h, $f_h);
			$src_x = 0;
			$src_y = 0;
			$dst_w = $letter_width * ($letter_width / $f_w);
			$dst_h = self::$height * (self::$height / $f_h);
			$src_w = $letter_width;
			$src_h = self::$height;

			imagecopyresized($captcha, $letter_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

			$xs += $letter_width;
			imagedestroy($letter_img);
		}

		return $captcha;
	}


	/**
	 * Draw circles on captcha background
	 *
	 * @param $captcha
	 *
	 * @return mixed
	 */
	private static function circles($captcha) {

		$min = min(self::$width, self::$height);
		$max = max(self::$width, self::$height);

		$scream = mt_rand(1,mb_strlen(self::$code));

		for($i=0;$i<=mt_rand(1,$scream);$i++) {
			$r = mt_rand(100,255);
			$g = mt_rand(100,255);
			$b = mt_rand(100,255);
			$color = imagecolorallocatealpha($captcha, $r, $g, $b, mt_rand(55,75));

			$radius = mt_rand($max/5,$max/1.25);
			$x = mt_rand(1,self::$width-1); mt_srand();
			$y = mt_rand(1,self::$height-1); mt_srand();

			imagefilledellipse($captcha, $x, $y, $radius, $radius, $color);
		}

		return $captcha;
	}


	/**
	 * Draw lines on captcha background
	 *
	 * @param $captcha
	 *
	 * @return mixed
	 */
	private static function polygons($captcha) {

		$min = min(self::$width, self::$height);
		$max = max(self::$width, self::$height);

		$scream = mt_rand(0,round($max/$min));

		for($i=0;$i<=$scream;$i++) {
			mt_srand();
			$r = mt_rand(0,150);
			$g = mt_rand(0,150);
			$b = mt_rand(0,150);
			$color = imagecolorallocatealpha($captcha, $r, $g, $b, mt_rand(20,70));

			$points = array();
			$p = mt_rand(3,9);
			for($s=0;$s<=$p;$s++) {
				$points[] = mt_rand(-50,self::$width+50); mt_srand();
				$points[] = mt_rand(-50,self::$height+50); mt_srand();
			}
			imagepolygon($captcha, $points, $p, $color);
		}

		return $captcha;
	}


	/**
	 * Valid and set code string
	 *
	 * @param $code
	 */
	private static function set_code($code) {

		$condition = self::get_condition();

		$code = preg_replace(array('(\W+)','([^'.$condition.'])'), array('',''), $code);

		self::$code = $code;
	}


	/**
	 * Get pcre condition for code string
	 *
	 * @return string
	 */
	private static function get_condition() {

		$condition = "";

		if(self::$use_lower_letter) {
			$condition .= "a-z";
		}
		if(self::$use_upper_letter) {
			$condition .= "A-Z";
		}
		if(self::$use_number) {
			$condition .= "0-9";
		}

		return $condition;
	}
}
