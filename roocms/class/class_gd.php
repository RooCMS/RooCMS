<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see https://www.gnu.org/licenses/
 */

//#########################################################
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	http_response_code(403);
	header('Content-Type: text/plain; charset=utf-8');
	exit('403:Access denied');
}
//#########################################################


/**
 * Class GD
 * Handles image processing and manipulation using GD library
 * Reanimated from 1.4
 */
class GD {

	use GDExtends;

	private SiteSettings $siteSettings;

	# Constants
	private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
	private const FILE_SUFFIX_ORIGINAL = '_orig';
	private const FILE_SUFFIX_RESIZED = '_res';
	private const FILE_SUFFIX_THUMBNAIL = '_thumb';
	private const DEFAULT_FONT_PATH = _STORAGE . "/fonts/trebuc.ttf";
	private const WATERMARK_MAX_SIZE_RATIO = 0.33;
	private const WATERMARK_PADDING = 10;
	private const TEXT_WATERMARK_FONT_SIZE = 10;
	private const TEXT_WATERMARK_ALPHA = 20;

	# Settings cache
	private array $settings_cache = [];

	public array $info = [];					# GD info
	public string $copyright = "";				# Copyright text ( Default: site_name )
	public string $domain = "";					# Site address ( Default: SERVER_NAME )
	private int $rs_quality = 90;				# Quality saved image
	private int $th_quality = 90;				# Quality thumbnail
	private string $thumbtg = "cover";			# Type Thumbnail ( Variables: cover, contain )
	private array $thumbbgcol = ['r' => 0, 'g' => 0, 'b' => 0];	# Background color for thumbnail "contain" type


	/**
	 * Constructor - Initialize GD settings
	 * 
	 * @param SiteSettings $siteSettings Site settings
	 */
	public function __construct(SiteSettings $siteSettings) {

		$this->siteSettings = $siteSettings;

		# Get GD info
		$this->info = gd_info();

		# Set thumbnail sizes from configuration
		$thumb_width = $this->get_setting('gd_thumb_image_width');
		$thumb_height = $this->get_setting('gd_thumb_image_height');
		$this->set_mod_sizes([$thumb_width, $thumb_height]);

		# Set max size
		$max_width = $this->get_setting('gd_image_maxwidth');
		$max_height = $this->get_setting('gd_image_maxheight');
		$this->set_mod_sizes([$max_width, $max_height], "msize");

		# Set thumbnail type
		if($this->get_setting('gd_thumb_type_gen') === "contain") {
			$this->thumbtg = "contain";
		}

		# Background color from configuration
		$bgcolor = $this->get_setting('gd_thumb_bgcolor');
		if(mb_strlen($bgcolor) === 7) {
			$this->thumbbgcol = cvrt_color_h2d($bgcolor);
		}

		# Quality thumbnail from configuration
		$quality = $this->get_setting('gd_thumb_jpg_quality');
		if($quality >= 10 && $quality <= 100) {
			$this->th_quality = $quality;
		}

		# if use watermark
		if($this->get_setting('gd_use_watermark') === "text") {

			# watermark text string one
			$this->copyright = sanitize_string($this->get_setting('site_name'));
			$watermark_one = $this->get_setting('gd_watermark_string_one');
			if(trim($watermark_one) !== "") {
				$this->copyright = sanitize_string($watermark_one);
			}

			# watermark text string two
			$this->domain = $_SERVER['SERVER_NAME'];
			$watermark_two = $this->get_setting('gd_watermark_string_two');
			if(trim($watermark_two) !== "") {
				$this->domain = sanitize_string($watermark_two);
			}
		}
	}


	/**
	 * Get setting with caching
	 * 
	 * @param string $key Setting key
	 * @return mixed Setting value
	 */
	private function get_setting(string $key): mixed {
		return $this->settings_cache[$key] ??= $this->siteSettings->get_by_key($key);
	}


	/**
	 * Validate image extension
	 * 
	 * @param string $ext Extension to validate
	 * @throws \InvalidArgumentException If extension is not supported
	 */
	private function validate_extension(string $ext): void {
		if(!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
			throw new \InvalidArgumentException("Unsupported image extension: {$ext}");
		}
	}


	/**
	 * Setup alpha channel and background for image
	 *
	 * @param \GdImage $image Image resource
	 * @param string   $ext   File extension
	 * @return int Background color resource
	 */
	private function setup_alpha_background(\GdImage $image, string $ext): int {
		$alpha = $this->is_gifpng($ext) ? 127 : 0;
		$bgcolor = imagecolorallocatealpha($image, $this->thumbbgcol['r'], $this->thumbbgcol['g'], $this->thumbbgcol['b'], $alpha);

		if($this->is_gifpng($ext)) {
			imagecolortransparent($image, $bgcolor);
		}

		return $bgcolor;
	}


	/**
	 * Build file path
	 *
	 * @param string $path     Base path
	 * @param string $filename Filename
	 * @param string $suffix   File suffix
	 * @param string $ext      File extension
	 * @return string Full file path
	 */
	private function build_file_path(string $path, string $filename, string $suffix, string $ext): string {
		return $path . "/" . $filename . $suffix . "." . $ext;
	}


	/**
	 * Get image size safely
	 *
	 * @param string $file_path Full path to image file
	 * @return array{0: int, 1: int} Image dimensions [width, height]
	 * @throws \RuntimeException If file doesn't exist or getimagesize fails
	 */
	private function get_image_size_safe(string $file_path): array {
		if(!file_exists($file_path)) {
			throw new \RuntimeException("Image file not found: {$file_path}");
		}

		$size = @getimagesize($file_path);
		if($size === false) {
			throw new \RuntimeException("Failed to get image size: {$file_path}");
		}

		return $size;
	}


	/**
	 * Check and adjust memory limit for image processing
	 *
	 * @param int $width  Image width
	 * @param int $height Image height
	 */
	private function check_memory_limit(int $width, int $height): void {
		# Calculate required memory (width * height * 4 bytes per pixel * 1.65 safety factor)
		$required_memory = ceil($width * $height * 4 * 1.65);
		
		# Get current memory limit
		$memory_limit = ini_get('memory_limit');
		if($memory_limit === '-1') {
			return; # Unlimited memory
		}

		# Convert memory limit to bytes
		$memory_limit_bytes = convert_to_bytes($memory_limit);
		$current_usage = memory_get_usage(true);

		# Check if we have enough memory
		if(($current_usage + $required_memory) > $memory_limit_bytes) {
			# Try to increase memory limit
			$new_limit = ceil(($current_usage + $required_memory) / 1024 / 1024) . 'M';
			@ini_set('memory_limit', $new_limit);
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
	protected function modify_image(string $filename, string $extension, string $path, bool $watermark = true, bool $modify = true, bool $noresize = false): void {

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
		$watermark_type = $this->get_setting('gd_use_watermark');
		if($watermark_type !== "no" && $watermark) {

			# Text watermark
			if($watermark_type === "text") {
				$this->watermark_text($filename, $extension, $path);
			}

			# Graphic watermark
			if($watermark_type === "image") {
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
	protected function resize(string $filename, string $ext, string $path = _UPLOADIMAGES): void {

		# Build file paths
		$file_original = $this->build_file_path($path, $filename, self::FILE_SUFFIX_ORIGINAL, $ext);
		$file_resized = $this->build_file_path($path, $filename, self::FILE_SUFFIX_RESIZED, $ext);

		# Get image size safely
		$size = $this->get_image_size_safe($file_original);
		$w = $size[0];
		$h = $size[1];

		# Check memory limit for processing this image
		$this->check_memory_limit($w, $h);

		if($w <= $this->msize['w'] && $h <= $this->msize['h']) {
			copy($file_original, $file_resized);
		}
		else {
			# We carry out calculations for compression and reduction in size
			$ns = $this->calc_resize($w, $h, $this->msize['w'], $this->msize['h']);

			# Bring in memory blank image and original image for further work with them.
			$resize = $this->imgcreatetruecolor($ns['new_width'], $ns['new_height'], $ext);
			$bgcolor = $this->setup_alpha_background($resize, $ext);

			imagefilledrectangle($resize, 0, 0, $ns['new_width']-1, $ns['new_height']-1, $bgcolor);

			# Bring image in memory
			$src = $this->imgcreate($file_original, $ext);

			imagecopyresampled($resize, $src, 0, 0, 0, 0, $ns['new_width'], $ns['new_height'], $w, $h);

			# save image
			$this->imgsave($resize, $file_resized, $ext, $this->rs_quality);

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
	protected function resized(string $filename, string $ext, string $path = _UPLOADIMAGES): void {

		# vars
		$file = $filename.".".$ext;

		# Get image size
		$size = getimagesize($path."/".$file);

		# get orientation image (if possible)
		$orientation = $this->get_orientation($path."/".$file);

		# Bring in memory blank image and original image for further work with them.
		$resize = $this->imgcreatetruecolor($this->tsize['w'], $this->tsize['h'], $ext);
		$bgcolor = $this->setup_alpha_background($resize, $ext);

		imagefilledrectangle($resize, 0, 0, $this->tsize['w']-1, $this->tsize['h']-1, $bgcolor);

		# Bring image in memory
		$src = $this->imgcreate($path."/".$file, $ext);
		# ... and remove file
		unlink($path."/".$file);

		# We carry out calculations for compression and reduction in size
		$ns = $this->calc_resize($size[0], $size[1], $this->tsize['w'], $this->tsize['h'], false);
		$ns = $this->calc_newsize($ns);


		imagecopyresampled($resize, $src, $ns['new_left'], $ns['new_top'], 0, 0, $ns['new_width'], $ns['new_height'], $size[0], $size[1]);

		# rotate image based on EXIF orientation
		$resize = match($orientation) {
			3 => imagerotate($resize, 180, 0),
			6 => imagerotate($resize, -90, 0),
			8 => imagerotate($resize, 90, 0),
			default => $resize
		};

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
	protected function thumbnail(string $filename, string $ext, string $path = _UPLOADIMAGES): void {

		# vars
		$fileresize 	= $filename . self::FILE_SUFFIX_RESIZED . "." . $ext;
		$filethumb 	= $filename . self::FILE_SUFFIX_THUMBNAIL . "." . $ext;

		# Get image size
		$size = getimagesize($path."/".$fileresize);

		# Bring in memory blank image and original image for further work with them.
		$thumb = $this->imgcreatetruecolor($this->tsize['w'], $this->tsize['h'], $ext);
		$bgcolor = $this->setup_alpha_background($thumb, $ext);

		imagefilledrectangle($thumb, 0, 0, $this->tsize['w']-1, $this->tsize['h']-1, $bgcolor);

		# Bring image in memory
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# We carry out calculations thumbnail size
		$resize = $this->thumbtg !== "cover";
		$ns = $this->calc_resize($size[0], $size[1], $this->tsize['w'], $this->tsize['h'], $resize);

		# Recalculate for "cover" thumbnail
		if($this->thumbtg === "cover") {
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
	protected function watermark_text(string $filename, string $ext, string $path = _UPLOADIMAGES): void {

		# vars
		$fileresize = $filename . self::FILE_SUFFIX_RESIZED . "." . $ext;

		# get image size
		$size = getimagesize($path."/".$fileresize);

		# Bring image in memory
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# erase original
		unlink($path."/".$fileresize);

		# Colors for text watermark
		$shadow = imagecolorallocatealpha($src, 0, 0, 0, self::TEXT_WATERMARK_ALPHA);
		$color  = imagecolorallocatealpha($src, 255, 255, 255, self::TEXT_WATERMARK_ALPHA);

		# Font settings
		$angle = 0;
		$fontsize = self::TEXT_WATERMARK_FONT_SIZE;
		$fontfile = self::DEFAULT_FONT_PATH;

		# Draw copyright text
		if(trim($this->copyright) !== "") {
			$this->draw_text_with_shadow($src, $fontsize, $angle, 7, $size[1] - 18, $shadow, $color, $fontfile, $this->copyright);
		}

		# Draw domain text
		if(trim($this->domain) !== "") {
			$this->draw_text_with_shadow($src, $fontsize, $angle, 7, $size[1] - 5, $shadow, $color, $fontfile, $this->domain);
		}

		# save with watermark
		$this->imgsave($src, $path."/".$fileresize, $ext, $this->rs_quality);

		imagedestroy($src);
	}


	/**
	 * Draw text with shadow effect
	 *
	 * @param \GdImage $image       - image resource
	 * @param int      $size        - font size
	 * @param int      $angle       - text angle
	 * @param int      $x           - x coordinate
	 * @param int      $y           - y coordinate
	 * @param int      $shadow_color - shadow color
	 * @param int      $text_color  - text color
	 * @param string   $font        - font path
	 * @param string   $text        - text to draw
	 */
	private function draw_text_with_shadow(\GdImage $image, int $size, int $angle, int $x, int $y, int $shadow_color, int $text_color, string $font, string $text): void {

		# Shadow offsets for 3D effect
		$offsets = [[1, 1], [-1, -1], [1, -1], [-1, 1]];

		foreach($offsets as [$dx, $dy]) {
			imagettftext($image, $size, $angle, $x + $dx, $y + $dy, $shadow_color, $font, $text);
		}

		# Main text
		imagettftext($image, $size, $angle, $x, $y, $text_color, $font, $text);
	}


	/**
	 * Create and place image watermark
	 *
	 * @param string $filename - file name
	 * @param string $ext      - file extension (without dot)
	 * @param string $path     - path to file
	 */
	protected function watermark_image(string $filename, string $ext, string $path = _UPLOADIMAGES): void {

		# vars
		$fileresize = $filename . self::FILE_SUFFIX_RESIZED . "." . $ext;

		# get image size
		$size = getimagesize($path."/".$fileresize);
		$w = $size[0];
		$h = $size[1];

		# get data file for modify
		$src = $this->imgcreate($path."/".$fileresize, $ext);

		# remove original
		unlink($path."/".$fileresize);

		# watermark
		$wm_image_path = $path . "/" . $this->get_setting('gd_watermark_image');
		$wminfo = pathinfo($wm_image_path);
		$wmsize = getimagesize($wm_image_path);
		$ww = $wmsize[0];
		$wh = $wmsize[1];
		$watermark = $this->imgcreate($wm_image_path, $wminfo['extension']);

		# Calculate size watermark for modify (max 33% of image size)
		$maxwmw = floor($w * self::WATERMARK_MAX_SIZE_RATIO);
		$wp = 0;
		if($ww >= $maxwmw) {
			$wp = percent($maxwmw, $ww);
		}

		$maxwmh = floor($h * self::WATERMARK_MAX_SIZE_RATIO);
		$hp = 0;
		if($wh >= $maxwmh) {
			$hp = percent($maxwmh, $wh);
		}

		$pr = ($wp !== 0 || $hp !== 0) ? max($wp, $hp) / 100 : 1;

		$wms = $this->calc_resize($ww, $wh, $ww * $pr, $wh * $pr, false);

		# Position watermark at bottom-right corner with padding
		$x = $w - ($wms['new_width'] + self::WATERMARK_PADDING);
		$y = $h - ($wms['new_height'] + self::WATERMARK_PADDING);

		imagecopyresized($src, $watermark, $x, $y, 0, 0, $wms['new_width'], $wms['new_height'], $ww, $wh);

		# save with watermark
		$this->imgsave($src, $path."/".$fileresize, $ext, $this->rs_quality);

		imagedestroy($src);
		imagedestroy($watermark);
	}


	/**
	 * Convert jpg to webp
	 *
	 * @param string $filename - file name
	 * @param string $ext      - file extension (without dot)
	 * @param string $path     - path to file
	 *
	 * @return string          - return result extension
	 */
	protected function convert_jpgtowebp(string $filename, string $ext, string $path = _UPLOADIMAGES): string {

		if($this->is_jpg($ext)) {

			if(is_file($path."/".$filename . self::FILE_SUFFIX_ORIGINAL . "." . $ext)) {
				$filename = $filename . self::FILE_SUFFIX_ORIGINAL;
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
	 * @param string $ext  - file extension without dot
	 *
	 * @return \GdImage
	 * @throws \RuntimeException If image file not found or cannot be created
	 * @throws \InvalidArgumentException If extension is not supported
	 */
	private function imgcreate(string $from, string $ext): \GdImage {

		# Validate extension
		$this->validate_extension($ext);

		# Check if file exists and is readable
		if(!file_exists($from) || !is_readable($from)) {
			throw new \RuntimeException("Image file not found or not readable: {$from}");
		}

		# Create image from file based on extension
		$src = match($ext) {
			'webp' => @imagecreatefromwebp($from),
			'gif' => @imagecreatefromgif($from),
			'png' => @imagecreatefrompng($from),
			default => @imagecreatefromjpeg($from), # jpg
		};

		# Check if image creation was successful
		if($src === false) {
			throw new \RuntimeException("Failed to create image from file: {$from}");
		}

		# Setup alpha channel for transparent images
		if($ext === 'gif') {
			imagealphablending($src, false);
			imagesavealpha($src, true);
		}
		elseif($ext === 'png') {
			imagealphablending($src, true);
			imagesavealpha($src, true);
		}

		return $src;
	}


	/**
	 * Create blank image source
	 *
	 * @param int    $width  - width blank
	 * @param int    $height - height blank
	 * @param string $ext    - extension
	 *
	 * @return \GdImage
	 * @throws \RuntimeException If image creation fails
	 */
	private function imgcreatetruecolor(int $width, int $height, string $ext): \GdImage {

		# Validate dimensions
		if($width <= 0 || $height <= 0) {
			throw new \InvalidArgumentException("Invalid image dimensions: {$width}x{$height}");
		}

		$src = @imagecreatetruecolor($width, $height);

		# Check if image creation was successful
		if($src === false) {
			throw new \RuntimeException("Failed to create image with dimensions: {$width}x{$height}");
		}

		# Setup alpha channel for transparent images
		if($this->is_gifpng($ext)) {
			imagealphablending($src, false);
			imagesavealpha($src, true);
		}

		return $src;
	}


	/**
	 * Image save
	 *
	 * @param \GdImage $res     - image
	 * @param string   $path    - path for save
	 * @param string   $ext     - extension
	 * @param int      $quality - quality
	 */
	private function imgsave(\GdImage $res, string $path, string $ext, int $quality = 0): void {
		match($ext) {
			'webp' => imagewebp($res, $path, $quality),
			'gif' => imagegif($res, $path),
			'png' => imagepng($res, $path),
			default => imagejpeg($res, $path, $quality), # jpg
		};
	}
}
