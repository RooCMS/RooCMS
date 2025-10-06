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
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################



/**
 * Trait GDExtends extends class GD
 */
trait GDExtends {


	/**
	 * Check extension on gif or png
	 *
	 * @param string $ext - extension
	 * @return bool
	 */
	protected function is_gifpng(string $ext): bool {
		return $ext === "gif" || $ext === "png";
	}


	/**
	 * Check extension on jpg or jpeg
	 *
	 * @param string $ext - extension
	 * @return bool
	 */
	protected function is_jpg(string $ext): bool {
		return $ext === "jpg" || $ext === "jpeg";
	}


	/**
	 * Get image orientation
	 *
	 * @param string $image - path to image
	 * @return int
	 */
	protected function get_orientation(string $image): int {

		$orientation = 1;

		if(function_exists('exif_read_data') && exif_imagetype($image) === 2) {
			$exif = exif_read_data($image);
			if(isset($exif['Orientation'])) {
				$orientation = $exif['Orientation'];
			}
		}

		return $orientation;
	}


	/**
	 * Validate image extension
	 *
	 * @param string $ext Extension to validate
	 * @throws InvalidArgumentException If extension is not supported
	 * TODO: Remove or move to MediaImage class
	 */
	protected function validate_extension(string $ext): void {
		if(!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
			throw new InvalidArgumentException("Unsupported image extension: {$ext}");
		}
	}


	/**
	 * Convert limit string to bytes
	 * 
	 * @param string $value Limit string (e.g., "128M", "1G")
	 * @return int Limit in bytes
	 */
	protected function convert_to_bytes(string $value): int {
		$value = trim($value);
		$unit = strtolower($value[strlen($value) - 1]);
		$num = (int) $value;

		return match($unit) {
			'g' => $num * 1024 * 1024 * 1024,
			'm' => $num * 1024 * 1024,
			'k' => $num * 1024,
			default => $num
		};
	}


	/**
	 * Calculate new image sizes
	 *
	 * @param int  $width    - Current width
	 * @param int  $height   - Current height
	 * @param int  $towidth  - Required width
	 * @param int  $toheight - Required height
	 * @param bool $resize   - Flag indicating proportional resize (true) or crop (false)
	 * @return array{new_width: int, new_height: int, new_left: int, new_top: int}
	 */
	protected function calc_resize(int $width, int $height, int $towidth, int $toheight, bool $resize = true): array {

		$x_ratio = $towidth / $width;
		$y_ratio = $toheight / $height;
		$ratio = $resize ? min($x_ratio, $y_ratio) : max($x_ratio, $y_ratio);
		$use_x_ratio = ($x_ratio === $ratio);
		$new_width = $use_x_ratio ? $towidth : floor($width * $ratio);
		$new_height = !$use_x_ratio ? $toheight : floor($height * $ratio);
		$new_left = $use_x_ratio ? 0 : floor(($towidth - $new_width) / 2);
		$new_top = !$use_x_ratio ? 0 : floor(($toheight - $new_height) / 2);

		return [
			'new_width'  => (int) $new_width,
			'new_height' => (int) $new_height,
			'new_left'   => (int) $new_left,
			'new_top'    => (int) $new_top
		];
	}


	/**
	 * Calculate new size
	 *
	 * @param array{new_width: int, new_height: int, new_left: int, new_top: int} $ns - array new size
	 * @return array{new_width: int, new_height: int, new_left: int, new_top: int}
	 */
	protected function calc_newsize(array $ns): array {

		if($ns['new_left'] > 0) {
			$ns['new_top'] = $ns['new_top'] - $ns['new_left'];
			$proc = (($ns['new_left'] * 2) / $ns['new_width']);
			$ns['new_width']  = ($ns['new_width'] + ($ns['new_width'] * $proc)) + 2;
			$ns['new_height'] = ($ns['new_height'] + ($ns['new_height'] * $proc)) + 2;
			$ns['new_left'] = 0;
		}

		if($ns['new_top'] > 0) {
			$ns['new_left'] = $ns['new_left'] - $ns['new_top'];
			$proc = (($ns['new_top'] * 2) / $ns['new_height']);
			$ns['new_width']  = ($ns['new_width'] + ($ns['new_width'] * $proc)) + 2;
			$ns['new_height'] = ($ns['new_height'] + ($ns['new_height'] * $proc)) + 2;
			$ns['new_top'] = 0;
		}

		return $ns;
	}
}
