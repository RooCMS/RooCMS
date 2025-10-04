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
 * Class ThemeConfig
 * Base implementation of theme configuration
 */
class ThemeConfig implements ThemeConfigInterface {

	private string $theme_path; 	// Theme path
	private string $theme_name; 	// Theme name
	private string $renderer_type; 	// Renderer type
	private array $config; 			// Theme config



	/**
	 * Constructor
	 * @param string $theme_name Theme name
	 * @param string $themes_dir Themes directory
	 */
	public function __construct(string $theme_name, string $themes_dir = 'themes') {
		$this->theme_name = $theme_name;
		$this->theme_path = $themes_dir . '/' . $theme_name;
		$this->config = [];

		$this->load_config();
	}


	/**
	 * Loads the theme configuration
	 * @return void
	 * @throws Exception If the theme configuration file is not found
	 */
	private function load_config(): void {
		$config_file = $this->theme_path . '/theme.json';

		if (is_file($config_file)) {
			$config_data = json_decode(read_file($config_file), true);
			if (is_array($config_data)) {
				$this->config = $config_data;
			}
		}

		// Determine the renderer type by the presence of files
		if (is_dir($this->theme_path . '/layouts') && is_file($this->theme_path . '/layouts/base.php')) {
			$this->renderer_type = 'php';
		} elseif (is_dir($this->theme_path . '/layouts') && is_file($this->theme_path . '/layouts/base.html')) {
			$this->renderer_type = 'html';
		} else {
			$this->renderer_type = 'php'; // by default
		}
	}

	
	/**
	 * @inheritDoc
	 */
	public function get_theme_path(): string {
		return $this->theme_path;
	}

	/**
	 * @inheritDoc
	 */
	public function get_theme_web_path(): string {
		return '/themes/' . $this->theme_name;
	}

	/**
	 * @inheritDoc
	 */
	public function get_theme_name(): string {
		return $this->theme_name;
	}

	/**
	 * @inheritDoc
	 */
	public function get_renderer_type(): string {
		return $this->renderer_type;
	}

	/**
	 * @inheritDoc
	 */
	public function get_config(): array {
		return $this->config;
	}

	/**
	 * @inheritDoc
	 */
	public function exists(): bool {
		return is_dir($this->theme_path);
	}

	/**
	 * @inheritDoc
	 */
	public function get_cache_settings(): array {
		return $this->cache_settings;
	}

}
