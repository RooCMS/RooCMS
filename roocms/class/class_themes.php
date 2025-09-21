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
 * Class Themes
 * Improved theme management system with support for PHP and HTML templates
 */
class Themes {

	private array $renderers = []; 						// TemplateRenderer Available renderers: php/html
	private ?ThemeConfig $current_theme_config = null;	// Current theme configuration
	private string $themes_dir; 						// Directory with themes
	private array $global_vars = []; 					// Global variables for all templates: site_name, site_description, base_url, current_url
	private $theme_config_factory; 						// callable(string $theme_name, string $themes_dir): ThemeConfig



	/**
	 * Constructor
	 * @param TemplateRendererPhp $php_renderer PHP renderer
	 * @param TemplateRendererHtml $html_renderer HTML renderer
	 * @param string $themes_dir Themes directory
	 * @param callable|null $theme_config_factory Theme config factory
	 */
	public function __construct(TemplateRendererPhp $php_renderer, TemplateRendererHtml $html_renderer, string $themes_dir = 'themes', ?callable $theme_config_factory = null) {
		$this->themes_dir = $themes_dir;
		// First global variables, then bind renderers
		$this->initialize_global_vars();
		$this->initialize_renderers($php_renderer, $html_renderer);
		$this->theme_config_factory = $theme_config_factory ?? function(string $theme_name, string $themes_dir): ThemeConfig {
			return new ThemeConfig($theme_name, $themes_dir);
		};
	}


	/**
	 * Initializes available renderers
	 * @param TemplateRendererPhp $php_renderer PHP renderer
	 * @param TemplateRendererHtml $html_renderer HTML renderer
	 */
	private function initialize_renderers(TemplateRendererPhp $php_renderer, TemplateRendererHtml $html_renderer): void {
		$this->renderers['php'] = $php_renderer;
		$this->renderers['html'] = $html_renderer;

		// Set global variables for renderers
		foreach ($this->renderers as $renderer) {
			if (method_exists($renderer, 'set_global_vars')) {
				$renderer->set_global_vars($this->global_vars);
			}
		}
	}


	/**
	 * Initializes global variables
	 * @return void
	 */
	private function initialize_global_vars(): void {
		$this->global_vars = [
			'site_name' => 'RooCMS',
			'site_description' => 'Open Source Content Management System',
			'csp_nonce' => CSPNONCE,
			'base_url' => $this->get_base_url(),
			'current_url' => $this->get_current_url(),
		];
	}


	/**
	 * Sets the current theme
	 * @param string $theme_name Theme name
	 * @return bool Success of setting
	 * @throws Exception If theme config factory is not set
	 */
	public function set_theme(string $theme_name): bool {
		$this->current_theme_config = ($this->theme_config_factory)($theme_name, $this->themes_dir);

		if (!$this->current_theme_config->exists()) {
			return false;
		}

		return true;
	}


	/**
	 * Renders the page
	 * @param string $path Page path
	 * @param array $data Additional data
	 * @return bool Success of rendering
	 * @throws Exception If theme config factory is not set
	 */
	public function render(string $path, array $data = []): bool {
		//TODO: add debug mode
		if ($this->current_theme_config === null && !$this->auto_detect_theme()) {
			return false;
		}

		$renderer = $this->renderers[$this->current_theme_config->get_renderer_type()];

		// Add theme data
		$theme_data = [
			'theme_name' => $this->current_theme_config->get_theme_name(),
			'theme_base' => $this->current_theme_config->get_theme_web_path(),
		];

		$final_data = array_merge($theme_data, $data);

		return $renderer->render($this->current_theme_config->get_theme_path(), $path, $final_data);
	}


	/**
	 * Renders the page with the specified theme
	 * @param string $theme_name Theme name
	 * @param string $path Page path
	 * @param array $data Additional data
	 * @return bool Success of rendering
	 * @throws Exception If theme config factory is not set
	 */
	public function render_with_theme(string $theme_name, string $path, array $data = []): bool {
		$original_theme = $this->current_theme_config;

		if (!$this->set_theme($theme_name)) {
			return false;
		}

		$result = $this->render($path, $data);

		// Restore original theme
		$this->current_theme_config = $original_theme;

		return $result;
	}


	/**
	 * Checks if the template exists
	 * @param string $path Page path
	 * @param string|null $theme_name Theme name (optional)
	 * @return bool Existence of template
	 * @throws Exception If theme config factory is not set
	 */
	public function template_exists(string $path, ?string $theme_name = null): bool {
		if ($theme_name) {
			$theme_config = ($this->theme_config_factory)($theme_name, $this->themes_dir);
			if (!$theme_config->exists()) {
				return false;
			}
		} else {
			$theme_config = $this->current_theme_config ?? ($this->auto_detect_theme() ? $this->current_theme_config : null);
			if (!$theme_config) {
				return false;
			}
		}

		$renderer = $this->renderers[$theme_config->get_renderer_type()];
		return $renderer->template_exists($theme_config->get_theme_path(), $path);
	}


	/**
	 * Returns the list of available themes
	 * @return array List of themes
	 * @throws Exception If theme config factory is not set
	 */
	public function get_available_themes(): array {
		$themes = [];

		if (!is_dir($this->themes_dir)) {
			return $themes;
		}

		$items = scandir($this->themes_dir);
		if ($items === false) {
			return $themes;
		}

		foreach ($items as $item) {
			if ($item === '.' || $item === '..' || !is_dir($this->themes_dir . '/' . $item)) {
				continue;
			}

			$theme_config = ($this->theme_config_factory)($item, $this->themes_dir);
			if ($theme_config->exists()) {
				$themes[$item] = [
					'name' => $item,
					'type' => $theme_config->get_renderer_type(),
					'path' => $theme_config->get_theme_path(),
					'config' => $theme_config->get_config()
				];
			}
		}

		return $themes;
	}


	/**
	 * Sets the global variable
	 * @param string $key Key
	 * @param mixed $value Value
	 * @throws Exception If theme config factory is not set
	 */
	public function set_global_var(string $key, mixed $value): void {
		$this->global_vars[$key] = $value;

		// Update global variables in all renderers
		foreach ($this->renderers as $renderer) {
			if (method_exists($renderer, 'set_global_var')) {
				$renderer->set_global_var($key, $value);
			}
		}
	}


	/**
	 * Gets the global variable
	 * @param string $key Key
	 * @param mixed $default Default value
	 * @return mixed Value
	 * @throws Exception If theme config factory is not set
	 */
	public function get_global_var(string $key, mixed $default = null): mixed {
		return $this->global_vars[$key] ?? $default;
	}


	/**
	 * Returns the current theme configuration
	 * @return ThemeConfig|null Theme configuration
	 * @throws Exception If theme config factory is not set
	 */
	public function get_current_theme_config(): ?ThemeConfig {
		return $this->current_theme_config;
	}


	/**
	 * Automatically determines the theme
	 * @return bool Success of determination
	 * @throws Exception If theme config factory is not set
	 */
	private function auto_detect_theme(): bool {
		$themes = $this->get_available_themes();

		// Priority: default, then the first available
		if (isset($themes['default'])) {
			return $this->set_theme('default');
		}

		if (!empty($themes)) {
			$first_theme = array_key_first($themes);
			return $this->set_theme($first_theme);
		}

		return false;
	}


	/**
	 * Returns the base URL
	 * @return string Base URL
	 */
	private function get_base_url(): string {
		$protocol = (!empty(env('HTTPS')) && env('HTTPS') !== 'off') ? 'https' : 'http';
		$host = env('HTTP_HOST') ?? env('SERVER_NAME') ?? 'localhost';
		return $protocol . '://' . $host;
	}

	
	/**
	 * Returns the current URL
	 * @return string Current URL
	 */
	private function get_current_url(): string {
		$requestUri = env('REQUEST_URI') ?? '/';
		return $this->get_base_url() . $requestUri;
	}

}