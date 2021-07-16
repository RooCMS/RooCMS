<?php
/**
* aR Captcha - Protect Form v3.2
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
	private static $width   = 170;
	private static $height  = 70;

	# user settings
	private static $use_polygons    = false;  // no stable
	private static $use_fontsnoise  = true;   // in test
	private static $wave_effect     = false;  // in process development
	private static $shuffle_font    = false;
	private static $effect_sketch   = false;
	private static $effect_pixelate = false;

	# palette
	private static $palette = "aR-Captcha";
	private static $randoms = array(0,24);

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
		//imagejpeg($captcha, _ROOCMS."/../_".$code."_".time().".jpg"); // debug
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

		# anti alias
		if(function_exists('imageantialias')) {
			imageantialias($captcha, true);
		}

		# interlace
		imageinterlace($captcha, true);

		# NOISE
		if(self::$use_fontsnoise && is_resource($captcha)) {
			$captcha = self::fontsnoise($captcha);
		}

		# letters
		$captcha = self::letters($captcha);

		# wave effect
		if(self::$wave_effect) {
			$captcha = self::waveeffect($captcha);
		}

		# NOISE
		if(self::$use_polygons && is_resource($captcha)) {
			$captcha = self::polygons($captcha);
		}

		# effects
		$captcha = self::effects($captcha);

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
			$color  = imagecolorallocatealpha($captcha, $r, $g, $b, mt_rand(0,20));
			//$colorsh  = imagecolorallocatealpha($captcha, $r/2, $g/2, $b/2, mt_rand(25,50));

			$angle = mt_rand(-15,15);

			$y = mt_rand(round(self::$height/1.4), self::$height);
			$size = mt_rand(floor(self::$height/1.75), ceil(self::$height/1.20));

			$letter = mb_substr(self::$code, $l, 1);

			switch($l) {
				case 0:
					$position = mt_rand($shift - round(self::$letter_width * .05), $shift + round(self::$letter_width * .25));
					break;

				case self::$code_length-1:
					$position = mt_rand($shift - round(self::$letter_width * .25), $shift + round(self::$letter_width * .05));
					break;

				default:
					$position = mt_rand($shift - round(self::$letter_width * .25), $shift + round(self::$letter_width * .25));
					break;
			}

			//imagettftext($captcha, $size, $angle, $position, $y-1, $colorsh, $font['file'], $letter);
			imagettftext($captcha, $size, $angle, $position, $y, $color, $font['file'], $letter);

			if(self::$shuffle_font) {
				$font = self::get_font();
			}

			$shift += self::$letter_width;
		}

		//imagecopy($captcha, $captcha, 0, 0, 0, 0, self::$width, self::$height);

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

		$scream = mt_rand(1,self::$code_length);

		for($i=0;$i<=$scream;$i++) {
			list($r,$g,$b) = self::$bgcolor;
			$color = imagecolorallocatealpha($captcha, $r, $g, $b, 0);

			$points = array();
			for($s=0;$s<=3;$s++) {
				$points[] = mt_rand(0,self::$width); mt_srand();
				$points[] = mt_rand(0,self::$height); mt_srand();
			}

			imageSetThickness($captcha, mt_rand(3,5));
			imagepolygon($captcha, $points, 3, $color);
			//imageLine($captcha, mt_rand(0,self::$width), mt_rand(0,self::$height), mt_rand(0,self::$width), mt_rand(0,self::$height), $color);
		}

		return $captcha;
	}


	/**
	 * Draw letters on BG Captcha
	 *
	 * @param resource $captcha
	 *
	 * @return resource
	 */
	private static function fontsnoise($captcha) {

		# get font
		$font = self::get_font();

		$shift = 4;
		for($l=0;$l<=self::$code_length;$l++) {
			list($r,$g,$b) = self::get_random_rgb();
			$color  = imagecolorallocatealpha($captcha, $r, $g, $b, mt_rand(85,95));

			$angle = mt_rand(-5,5);

			$y = mt_rand(round(self::$height/1.5), self::$height);
			$size = mt_rand(floor(self::$height/1.75), ceil(self::$height/1.5));

			$letter = randcode(1, "0123456789ABCDEFGHIJKLMNOPQRSTUVWXZ");

			$position = mt_rand($shift - round(self::$letter_width * .5), $shift + round(self::$letter_width * .5));

			imagettftext($captcha, $size, $angle, $position, $y, $color, $font['file'], $letter);

			if(self::$shuffle_font) {
				$font = self::get_font();
			}

			$shift += self::$letter_width;
		}

		return $captcha;
	}


	/**
	 * Wave effect
	 *
	 * @param resource $captcha
	 *
	 * @return resource
	 */
	private static function waveeffect($captcha) {

		$ncaptcha=imagecreatetruecolor(self::$width, self::$height);

		$rand1 = mt_rand(70000, 100000) / 1500000;
		$rand2 = mt_rand(70000, 100000) / 1500000;
		$rand3 = mt_rand(70000, 100000) / 1500000;
		$rand4 = mt_rand(70000, 100000) / 1500000;
		# phase
		$rand5 = mt_rand(0, 3141592) / 1000000;
		$rand6 = mt_rand(0, 3141592) / 1000000;
		$rand7 = mt_rand(0, 3141592) / 1000000;
		$rand8 = mt_rand(0, 3141592) / 1000000;
		# amp
		$rand9 = mt_rand(400, 600) / 100;
		$rand10 = mt_rand(400, 600) / 100;

		for($x = 0; $x < self::$width; $x++){
			for($y = 0; $y < self::$height; $y++){
				// coords source
				$sx = $x + ( sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6) ) * $rand9;
				$sy = $y + ( sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8) ) * $rand10;

				// back
				if($sx < 0 || $sy < 0 || $sx > self::$width || $sy > self::$height){
					$color = 255;
					$color_x = 255;
					$color_y = 255;
					$color_xy = 255;
				}else{ // antialias
					$color = (imagecolorat($captcha, $sx, $sy) >> 16) & 0xFF;
					$color_x = (imagecolorat($captcha, $sx + 1, $sy) >> 16) & 0xFF;
					$color_y = (imagecolorat($captcha, $sx, $sy + 1) >> 16) & 0xFF;
					$color_xy = (imagecolorat($captcha, $sx + 1, $sy + 1) >> 16) & 0xFF;
				}



				// сглаживаем только точки, цвета соседей которых отличается
				if($color == $color_x && $color == $color_y && $color == $color_xy){
					$newcolor = $color;
				}else{
					$frsx = $sx - floor($sx); //отклонение координат первообраза от целого
					$frsy = $sy - floor($sy);
					$frsx1 = 1 - $frsx;
					$frsy1 = 1 - $frsy;

					// вычисление цвета нового пикселя как пропорции от цвета основного пикселя и его соседей
					$newcolor = floor( $color    * $frsx1 * $frsy1 +
							   $color_x  * $frsx  * $frsy1 +
							   $color_y  * $frsx1 * $frsy  +
							   $color_xy * $frsx  * $frsy );
				}
				imagesetpixel($ncaptcha, $x, $y, imagecolorallocate($ncaptcha, $newcolor, $newcolor, $newcolor));
			}
		}

		return $ncaptcha;
	}


	/**
	 * Added another effects
	 *
	 * @param resource $captcha
	 *
	 * @return resource
	 */
	private static function effects($captcha) {

		# Cool Sketch Effect
		if(self::$effect_sketch) {
			imagefilter($captcha, IMG_FILTER_MEAN_REMOVAL);
		}

		if(self::$effect_pixelate) {
			imagefilter($captcha, IMG_FILTER_PIXELATE, ceil(max(self::$height, self::$width) / min(self::$height, self::$width)), true);
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

		mt_srand();
		$colorvariator = md5(self::$palette . mt_rand(self::$randoms[0], self::$randoms[1]));

		return array(
			hexdec(substr($colorvariator, 0, 2)),
			hexdec(substr($colorvariator, 2, 2)),
			hexdec(substr($colorvariator, 4, 2))
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


	/**
	 * Debug function for view palette
	 */
	public static function palette() {

		$nums = range(self::$randoms[0], self::$randoms[1]);

		$scale = 30;

		$im = imagecreatetruecolor($scale * count($nums), $scale);

		$shift = 0;
		foreach ($nums as $num) {

			$color = md5(self::$palette . $num);

			$r = hexdec(substr($color, 0, 2));
			$g = hexdec(substr($color, 2, 2));
			$b = hexdec(substr($color, 4, 2));

			$c = imagecolorallocate($im, $r, $g, $b);
			imagefilledrectangle($im, $scale * $shift, 0, $scale * ($shift + 1), $scale, $c);
			$shift++;
		}

		header('Content-Type: image/png');
		imagepng($im);
		imagedestroy($im);
	}
}
