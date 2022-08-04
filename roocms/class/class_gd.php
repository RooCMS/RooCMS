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


/**
 * Class GD
 */
class GD {

	use GDExtends;

	# vars
	public $info		= [];					# GD infoи
	public $copyright	= "";					# Copyright text ( Default: $site['title'] )
	public $domain		= "";					# Site address ( Default: $site['domain'] )
	private $rs_quality	= 90;					# Quality saved image
	private $th_quality	= 90;					# Quality thumbnail
	private $thumbtg	= "cover";				# Type Thumbnail ( Variables: cover, contain )
	private $thumbbgcol	= array('r' => 0, 'g' => 0, 'b' => 0);	# Background color for thumbnail "contain" type


	/**
	* Let's go
	*/
	public function __construct() {

		global $config, $site, $parse;

		# Get GD info
		$this->info = gd_info();

		# Set thumbnail sizes from configuration
		$this->set_mod_sizes(array($config->gd_thumb_image_width, $config->gd_thumb_image_height));

		# Set max size
		$this->set_mod_sizes(array($config->gd_image_maxwidth, $config->gd_image_maxheight), "msize");

		# Set thumbnail type
		if($config->gd_thumb_type_gen == "contain") {
			$this->thumbtg = "contain";
		}

		# Background color from cinfiguration
		if(mb_strlen($config->gd_thumb_bgcolor) == 7) {
			$this->thumbbgcol = $parse->cvrt_color_h2d($config->gd_thumb_bgcolor);
		}

		# Quality thumbnail from configuration
		if($config->gd_thumb_jpg_quality >= 10 && $config->gd_thumb_jpg_quality <= 100) {
			$this->th_quality = $config->gd_thumb_jpg_quality;
		}

		# if use watermark
		if($config->gd_use_watermark == "text") {

			# watermark text string one
			$this->copyright = $parse->text->html($site['title']);
			if(trim($config->gd_watermark_string_one) != "") {
				$this->copyright = $parse->text->html($config->gd_watermark_string_one);
			}

			# watermark text string two
			$this->domain = $_SERVER['SERVER_NAME'];
			if(trim($config->gd_watermark_string_two) != "") {
				$this->domain = $parse->text->html($config->gd_watermark_string_two);
			}
		}
	}


	/**
	 * Modify images
	 * Resize, create thumbnail, marked watermark.
	 *
	 * @param string $filename  - file name
	 * @param string $extension - file extension (without dot)
	 * @param string $path      - path to file
	 * @param bool   $watermark - on/off watermark
	 * @param bool   $modify    - this parameter indicates whether image is subjected to full modification with preserving the original image and creating thumbnail.
	 * @param bool   $noresize  - this parameter cancels "nomodify" image resizing, in case we want to keep original size.
	 */
	protected function modify_image(string $filename, string $extension, string $path, bool $watermark=true, bool $modify=true, bool $noresize=false) {

		global $config;

		# mod
		if($modify) {
			# image size change according to parameter settings
			$this->resize($filename, $extension, $path);

			# create thumbnail
			$this->thumbnail($filename, $extension, $path);
		}
		else {
			if(!$noresize) {
				$this->resized($filename, $extension, $path);
			}
		}


		# Set watermark on image
		if($config->gd_use_watermark != "no" && $watermark) {

			# Text watermark
			if($config->gd_use_watermark == "text" ) {
				$this->watermark_text($filename, $extension, $path);
			}

			# Graphic watermark
			if($config->gd_use_watermark == "image" ) {
				$this->watermark_image($filename, $extension, $path);
			}
		}
	}


	/**
	 * Resizing image
	 *
	 * @param string $filename - file name
	 * @param string $ext      - file extension (without dot)
	 * @param string $path     - path to file
	 */
	protected function resize(string $filename, string $ext, string $path=_UPLOADIMAGES) {

		# vars
		$fileoriginal 	= $filename."_original.".$ext;
		$fileresize 	= $filename."_resize.".$ext;


		# Get image size
		$size = getimagesize($path."/".$fileoriginal);
		$w = $size[0];
		$h = $size[1];

		if($w <= $this->msize['w'] && $h <= $this->msize['h']) {
			copy($path."/".$fileoriginal, $path."/".$fileresize);
		}
		else {
			# We carry out calculations for compression and reduction in size
			$ns = $this->calc_resize($w, $h, $this->msize['w'], $this->msize['h']);

			# Bring in memory blank image and original image for further work with them.
			$resize  = $this->imgcreatetruecolor($ns['new_width'], $ns['new_height'], $ext);

	        	$alpha   = ($this->is_gifpng($ext)) ? 127 : 0 ;
	        	$bgcolor = imagecolorallocatealpha($resize, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

	        	# alpha
			if($this->is_gifpng($ext)) {
				imagecolortransparent($resize, $bgcolor);
			}

			imagefilledrectangle($resize, 0, 0, $ns['new_width']-1, $ns['new_height']-1, $bgcolor);

			# Bring image in memory
			$src = $this->imgcreate($path."/".$fileoriginal, $ext);

            		imagecopyresampled($resize, $src, 0, 0, 0, 0, $ns['new_width'], $ns['new_height'], $w, $h);

			# save image
			$this->imgsave($resize, $path."/".$fileresize, $ext, $this->rs_quality);

			imagedestroy($resize);
			imagedestroy($src);
		}
	}


	/**
	 * Resize "nomodify" image
	 *
	 * @param string $filename - file name
	 * @param string $ext      - file extension (without dot)
	 * @param string $path     - path to file
	 */
	protected function resized(string $filename, string $ext, string $path=_UPLOADIMAGES) {

		# vars
		$file = $filename.".".$ext;

		# Get image size
		$size = getimagesize($path."/".$file);

		# get orientation image (if possible)
		$orientation = $this->get_orientation($path."/".$file);

		# Bring in memory blank image and original image for further work with them.
		$resize  = $this->imgcreatetruecolor($this->tsize['w'], $this->tsize['h'], $ext);
		$alpha   = ($this->is_gifpng($ext)) ? 127 : 0;

		$bgcolor = imagecolorallocatealpha($resize, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

		# alpha
		if($this->is_gifpng($ext)) {
			imagecolortransparent($resize, $bgcolor);
		}

		imagefilledrectangle($resize, 0, 0, $this->tsize['w']-1, $this->tsize['h']-1, $bgcolor);

		# Bring image in memory
		$src = $this->imgcreate($path."/".$file, $ext);
		# ... and remove file
		unlink($path."/".$file);

		# We carry out calculations for compression and reduction in size
		$ns = $this->calc_resize($size[0], $size[1], $this->tsize['w'], $this->tsize['h'], false);
		$ns = $this->calc_newsize($ns);


		imagecopyresampled($resize, $src, $ns['new_left'], $ns['new_top'], 0, 0, $ns['new_width'], $ns['new_height'], $size[0], $size[1]);


		# rotate image
		switch($orientation) {
			case 3:
				$resize = imagerotate($resize, 180, 0);
				break;
			case 6:
				$resize = imagerotate($resize, -90, 0);
				break;
			case 8:
				$resize = imagerotate($resize, 90, 0);
				break;
		}

		# save preview
		$this->imgsave($resize, $path."/".$file, $ext, $this->th_quality);

		imagedestroy($resize);
		imagedestroy($src);

	}


	/**
	 * Create thumbnail
	 *
	 * @param string $filename - file name
	 * @param string $ext      - file extension (without dot)
	 * @param string $path     - path to file
	 */
	protected function thumbnail(string $filename, string $ext, string $path=_UPLOADIMAGES) {

		# vars
        	$fileresize 	= $filename."_resize.".$ext;
        	$filethumb 	= $filename."_thumb.".$ext;

		# Get image size
		$size 		= getimagesize($path."/".$fileresize);

		# Bring in memory blank image and original image for further work with them.
		$thumb		= $this->imgcreatetruecolor($this->tsize['w'], $this->tsize['h'], $ext);

		$alpha 		= ($this->is_gifpng($ext)) ? 127 : 0 ;
        	$bgcolor	= imagecolorallocatealpha($thumb, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

		# alpha
		if($this->is_gifpng($ext)) {
			imagecolortransparent($thumb, $bgcolor);
		}

		imagefilledrectangle($thumb, 0, 0, $this->tsize['w']-1, $this->tsize['h']-1, $bgcolor);

		# Bring image in memory
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# We carry out calculations thumbnail size
		$resize = $this->thumbtg != "cover";
		$ns = $this->calc_resize($size[0], $size[1], $this->tsize['w'], $this->tsize['h'], $resize);

		# Recalculate for "cover" thumbnail
		if($this->thumbtg == "cover") {
			$ns = $this->calc_newsize($ns);
		}

		imagecopyresampled($thumb, $src, $ns['new_left'], $ns['new_top'], 0, 0, $ns['new_width'], $ns['new_height'], $size[0], $size[1]);

		# save preview
		$this->imgsave($thumb, $path."/".$filethumb, $ext, $this->th_quality);

		imagedestroy($thumb);
		imagedestroy($src);
	}


	/**
	 * Create and place text watermark
	 *
	 * @param string $filename - file name
	 * @param string $ext      - file extension (without dot)
	 * @param string $path     - path to file
	 */
	protected function watermark_text(string $filename, string $ext, string $path=_UPLOADIMAGES) {

		# vars
        	$fileresize = $filename."_resize.".$ext;

		# get image size
		$size = getimagesize($path."/".$fileresize);

		# Bring image in memory
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# erase original
		unlink($path."/".$fileresize);


		# Gaussian blur matrix:
		/*
		$matrix = array(
			array( 1, 2, 2 ),
			array( 2, 4, 2 ),
			array( 1, 2, 1 )
		);
		imageconvolution($src, $matrix, 16, 0);	*/


		# angle
		$angle = 0;

		# shadow text
		$shadow = imagecolorallocatealpha($src, 0, 0, 0, 20);
		$color  = imagecolorallocatealpha($src, 255, 255, 255, 20);

		# font size
		$fontsize = 10;

		# chose font
		$fontfile = ""._ROOCMS."/fonts/trebuc.ttf";

		if(trim($this->copyright) != "") {
			imagettftext($src, $fontsize, $angle, 7+1, $size[1]-18+1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $fontsize, $angle, 7-1, $size[1]-18-1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $fontsize, $angle, 7+1, $size[1]-18-1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $fontsize, $angle, 7-1, $size[1]-18+1, $shadow, $fontfile, $this->copyright);
			imagettftext($src, $fontsize, $angle, 7, $size[1]-18, $color, $fontfile, $this->copyright);
		}

		if(trim($this->domain) != "") {
			imagettftext($src, $fontsize, $angle, 7+1, $size[1]-5+1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $fontsize, $angle, 7-1, $size[1]-5-1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $fontsize, $angle, 7+1, $size[1]-5-1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $fontsize, $angle, 7-1, $size[1]-5+1, $shadow, $fontfile, $this->domain);
			imagettftext($src, $fontsize, $angle, 7, $size[1]-5, $color, $fontfile, $this->domain);
		}

		# save with watermark
		$this->imgsave($src, $path."/".$fileresize, $ext, $this->rs_quality);

        	imagedestroy($src);
	}


	/**
	 * Create and place image watermark
	 *
	 * @param string $filename - file name
	 * @param string $ext      - file extension (without dot)
	 * @param string $path     - path to file
	 */
	protected function watermark_image(string $filename, string $ext, string $path=_UPLOADIMAGES) {

		global $config, $parse;

		# vars
		$fileresize = $filename."_resize.".$ext;

		# get image size
		$size = getimagesize($path."/".$fileresize);
		$w = $size[0];
		$h = $size[1];

		# get data file for modify
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# remove original
		unlink($path."/".$fileresize);

		# watermark
		$wminfo = pathinfo($path."/".$config->gd_watermark_image);
		$wmsize = getimagesize($path."/".$config->gd_watermark_image);
		$ww = $wmsize[0];
		$wh = $wmsize[1];
		$watermark = $this->imgcreate($path."/".$config->gd_watermark_image, $wminfo['extension']);


		# Calculate size watermark for modify
		$maxwmw = floor($w*0.33); $wp = 0;
		if($ww >= $maxwmw) {
			$wp = $parse->percent($maxwmw, $ww);
		}

		$maxwmh = floor($h*0.33); $hp = 0;
		if($wh >= $maxwmh) {
			$hp = $parse->percent($maxwmh, $wh);
		}

		if($wp != 0 || $hp != 0) {
			$pr = max($wp, $hp)/100;
		}
		else {
			$pr = 1;
		}

		$wms = $this->calc_resize($ww, $wh, $ww*$pr, $wh*$pr, false);

		$x = $w - ($wms['new_width'] + 10);
		$y = $h - ($wms['new_height'] + 10);

		//imagecopyresampled($src, $watermark, $x, $y, 0, 0, $wms['new_width'], $wms['new_height'], $ww, $wh);
		imagecopyresized($src, $watermark, $x, $y, 0, 0, $wms['new_width'], $wms['new_height'], $ww, $wh);

		# save with watermark
		$this->imgsave($src, $path."/".$fileresize, $ext,  $this->rs_quality);

		imagedestroy($src);
		imagedestroy($watermark);
	}


	/**
	 * Convert jpf to webp
	 *
	 * @param string $filename - file name
	 * @param string $ext      - file extension (without dot)
	 * @param string $path     - path to file
	 *
	 * @return string          - return result extension
	 */
	protected function convert_jpgtowebp(string $filename, string $ext, string $path=_UPLOADIMAGES) {

		if($this->is_jpg($ext)) {

			if(is_file($path."/".$filename."_original.".$ext)) {
				$filename = $filename."_original";
			}

			# create
			$src = $this->imgcreate($path."/".$filename.".".$ext,$ext);

			# remove original
			unlink($path."/".$filename.".".$ext);

			# re:set ext for callback
			$ext = "webp";

			# save
			imagewebp($src,$path."/".$filename.".".$ext, $this->rs_quality);

			# destroy
			imagedestroy($src);
		}

		return $ext;
	}


	/**
	 * Get image source from image file
	 *
	 * @param string $from - full path and file name for craft
	 * @param string $ext  - fileextension without dot
	 *
	 * @return resource
	 */
	private function imgcreate(string $from, string $ext) {

		switch($ext) {
			case 'webp':
				$src = imagecreatefromwebp($from);
				break;

			case 'gif':
                		$src = imagecreatefromgif($from);
				imagealphablending($src, false);
				imagesavealpha($src,true);
				break;

			case 'png':
                		$src = imagecreatefrompng($from);
				imagealphablending($src, true);
				imagesavealpha($src,true);
				break;

			default: # jpg
				$src = imagecreatefromjpeg($from);
				break;
		}

		return $src;
	}


	/**
	 * Create blank image source
	 *
	 * @param int $width  - width blank
	 * @param int $height - height blank
	 * @param str $ext    - extension
	 *
	 * @return resource	-
	 */
	private function imgcreatetruecolor(int $width, int $height, str $ext) {

                $src = imagecreatetruecolor($width, $height);

		if($this->is_gifpng($ext)) {
	                imagealphablending($src, false);
			imagesavealpha($src,true);
		}

		return $src;
	}


	/**
	 * Image save
	 *
	 * @param resource $res     - image
	 * @param string   $path    - path for save
	 * @param string   $ext     - extension
	 * @param int      $quality - качество
	 */
	private function imgsave($res, string $path, string $ext, int $quality=0) {
		switch($ext) {
			case 'webp':
				imagewebp($res, $path, $quality);
				break;

			case 'gif':
				imagegif($res, $path);
				break;

			case 'png':
				imagepng($res, $path);
				break;

			default: #jpg
				imagejpeg($res, $path, $quality);
				break;
		}
	}
}
