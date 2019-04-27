<?php
/**
* aR Captcha - Protect Form v3.1
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
	private static $code = "00000";
	private static $code_length = 5;
	private static $letter_width = 0;

	# options for string
	private static $use_number       = true;
	private static $use_upper_letter = true;
	private static $use_lower_letter = false;

	# options for images

	# bg color
	private static $bgcolor = array(254, 254, 254);

	# sizes
	private static $width   = 200;
	private static $height  = 80;

	# allows
	private static $use_circles  = false;
	private static $use_polygons = true;
	private static $use_ttf      = true;
	private static $shuffle_font = false;

	# palette
	private static $palette = "aRCaptcha";
	private static $randoms = array(3,20);

	# fonts path
	private static $font_path = "fonts";


	/**
	 * Show
	 *
	 * @param string $code
	 */
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
		if(self::$use_polygons && is_resource($captcha)) {
			$captcha = self::polygons($captcha);
		}

		if(self::$use_circles && is_resource($captcha)) {
			$captcha = self::circles($captcha);
		}


		# letters
		$captcha = (self::$use_ttf) ? self::letters($captcha) : self::eletters($captcha) ;

		return $captcha;
	}


	/**
	 * Set code string on image (use ttf font)
	 *
	 * @param resource $captcha 	- resource image
	 *
	 * @return resource|false
	 */
	private static function letters($captcha) {

		# get font
		$font = self::get_font();

		$shift = 4;
		for($l=0;$l<=self::$code_length-1;$l++) {
			list($r,$g,$b) = self::get_random_rgb();
			$color  = imagecolorallocatealpha($captcha, $r, $g, $b, mt_rand(0,25));

			$angle = mt_rand(-15,15);

			$y = mt_rand(round(self::$height/1.5), self::$height);
			$size = mt_rand(floor(self::$height/2.5), ceil(self::$height/1.25));

			$letter = mb_substr(self::$code, $l, 1);
			imagettftext($captcha, $size, $angle, mt_rand($shift - round(self::$letter_width * .25), $shift + round(self::$letter_width * .5)), $y, $color, $font['file'], $letter);

			if(self::$shuffle_font) {
				$font = self::get_font();
			}

			$shift += self::$letter_width;
		}

		return $captcha;
	}


	/**
	 * Set code string on image (use default font)
	 *
	 * @param resource $captcha 	- resource image
	 *
	 * @return resource|false
	 */
	private static function eletters($captcha) {

		$shift = 4;
		for($l=0;$l<=self::$code_length-1;$l++) {
			$letter_img = imagecreate(self::$letter_width, self::$height);

			$bg = imagecolorallocate($letter_img, 255, 255, 255);
			imagefilledrectangle($letter_img, 0, 0, self::$letter_width, self::$height, $bg);
			imagecolortransparent($letter_img, $bg);

			list($r,$g,$b) = self::get_random_rgb();
			$color = imagecolorallocatealpha($letter_img, $r, $g, $b, mt_rand(0,50));

			# craft letter
			$letter = mb_substr(self::$code, $l, 1);
			imagestring($letter_img, 5, 0, 0, $letter, $color);

			# get font size
			$f_w = imagefontwidth(5);
			$f_h = imagefontheight(5);

			# set coords
			$dst_x = mt_rand($shift-3, $shift+3);
			$dst_y = mt_rand(0-$f_h, $f_h);
			$src_x = 0;
			$src_y = 0;
			$dst_w = self::$letter_width * (self::$letter_width / $f_w);
			$dst_h = self::$height * (self::$height / $f_h);
			$src_w = self::$letter_width;
			$src_h = self::$height;

			imagecopyresized($captcha, $letter_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

			$shift += self::$letter_width;
			imagedestroy($letter_img);
		}

		return $captcha;
	}


	/**
	 * Draw circles on captcha background
	 *
	 * @param resource $captcha
	 *
	 * @return resource|false
	 */
	private static function circles($captcha) {

		$max = max(self::$width, self::$height);

		$scream = mt_rand(1,ceil(mb_strlen(self::$code)/2));

		for($i=0;$i<=mt_rand(1,$scream);$i++) {
			mt_srand();
			list($r,$g,$b) = self::get_random_rgb();
			$color = imagecolorallocatealpha($captcha, $r, $g, $b, mt_rand(60,90));

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
	 * @param resource $captcha
	 *
	 * @return resource|false
	 */
	private static function polygons($captcha) {

		$min = min(self::$width, self::$height);
		$max = max(self::$width, self::$height);

		$scream = mt_rand(0,round($max/$min));

		for($i=0;$i<=$scream;$i++) {
			mt_srand();
			list($r,$g,$b) = self::get_random_rgb();
			$color = imagecolorallocatealpha($captcha, $r, $g, $b, mt_rand(25,50));

			$points = array();
			$p = mt_rand(3,9);
			for($s=0;$s<=$p;$s++) {
				$points[] = mt_rand(-50,self::$width+50); mt_srand();
				$points[] = mt_rand(-50,self::$height+50); mt_srand();
			}
			imagesetthickness($captcha, mt_rand(1,2));
			imagepolygon($captcha, $points, $p, $color);
		}

		return $captcha;
	}


	/**
	 * Valid and set code string
	 *
	 * @param string $code
	 */
	private static function set_code($code) {

		$condition = self::get_condition();

		$code = preg_replace(array('(\W+)','([^'.$condition.'])'), array('',''), $code);

		self::$code = $code;
		self::$code_length = mb_strlen(self::$code);
		self::$letter_width = (self::$width / self::$code_length >= self::$height) ? self::$height : (self::$width / self::$code_length) - 4 ;
	}


	/**
	 * Get random ttf font
	 *
	 * @return array
	 */
	private static function get_font() {

		# Select random fonts
		$fonts = glob(dirname(__FILE__)."/".self::$font_path."/*.ttf", GLOB_BRACE);

		# choice font
		$font = array();
		$font['file'] = $fonts[mt_rand(0,count($fonts)-1)];

		return $font;
	}


	/**
	 * Get random HEX RGB color
	 *
	 * @return array
	 */
	private static function get_random_rgb() {

		$hash = md5(self::$palette . mt_rand(self::$randoms[0], self::$randoms[1]));
		return array(
			hexdec(substr($hash, 0, 2)),
			hexdec(substr($hash, 2, 2)),
			hexdec(substr($hash, 4, 2))
		);
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
