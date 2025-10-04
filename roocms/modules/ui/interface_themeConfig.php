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
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################


/**
 * Interface ThemeConfigInterface
 * Defines the contract for theme configuration
 */
interface ThemeConfigInterface {

	/**
	 * Returns the base path to the theme
	 * @return string Base path
	 */
	public function get_theme_path(): string;

	/**
	 * Returns the web path to the theme
	 * @return string Web path
	 */
	public function get_theme_web_path(): string;

	/**
	 * Returns the name of the theme
	 * @return string Name of the theme
	 */
	public function get_theme_name(): string;

	/**
	 * Returns the type of the renderer for the theme
	 * @return string Type of the renderer
	 */
	public function get_renderer_type(): string;

	/**
	 * Returns the configuration of the theme
	 * @return array Configuration
	 */
	public function get_config(): array;

	/**
	 * Checks if the theme exists
	 * @return bool Existence of the theme
	 */
	public function exists(): bool;

	/**
	 * Returns the caching settings
	 * @return array Caching settings
	 */
	public function get_cache_settings(): array;
}
