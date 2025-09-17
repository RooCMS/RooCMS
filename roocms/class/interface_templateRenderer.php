<?php declare(strict_types=1);
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
//----------------------------------------------------------
if(!defined('RooCMS')) {
	http_response_code(403);
	header('Content-Type: text/plain; charset=utf-8');
	exit('403:Access denied');
}
//#########################################################


/**
 * Interface TemplateRenderer
 * Defines the contract for template renderers
 */
interface TemplateRenderer {

	/**
	 * Renders the page
	 * @param string $theme_base Base path to the theme
	 * @param string $path Path to the page
	 * @param array $data Data to pass to the template
	 * @return bool Success of rendering
	 */
	public function render(string $theme_base, string $path, array $data = []): bool;

	/**
	 * Checks the availability of the template
	 * @param string $theme_base Base path to the theme
	 * @param string $path Path to the page
	 * @return bool Availability of the template
	 */
	public function template_exists(string $theme_base, string $path): bool;

	/**
	 * Returns the supported file extensions
	 * @return array Array of extensions
	 */
	public function get_supported_extensions(): array;

	/**
	 * Returns the type of the renderer
	 * @return string Type of the renderer
	 */
	public function get_type(): string;
}
