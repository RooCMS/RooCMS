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
 * Class TemplateRendererPhp
 * Renderer for PHP templates
 */
class TemplateRendererPhp implements TemplateRenderer {

	private const SUPPORTED_EXTENSIONS = ['php'];  // Supported extensions
	private array $global_vars = []; // Global variables for all templates



	/**
	 * Sets global variables
	 * @param array $vars Global variables
	 */
	public function set_global_vars(array $vars): void {
		$this->global_vars = $vars;
	}


	/**
	 * Adds a global variable
	 * @param string $key Key
	 * @param mixed $value Value
	 */
	public function set_global_var(string $key, mixed $value): void {
		$this->global_vars[$key] = $value;
	}


	/**
	 * @inheritDoc
	 */
	public function render(string $theme_base, string $path, array $data = []): bool {
		$page_file = $this->get_page_file($theme_base, $path);

		if (!$this->template_exists($theme_base, $path)) {
			return false;
		}

		// Merge global variables with page data
		$template_vars = array_merge($this->global_vars, $data);

		// Set variables in the global scope
		foreach ($template_vars as $key => $value) {
			$$key = $value;
		}

		// Content header
		if (!headers_sent()) {
			header('Content-Type: text/html; charset=utf-8');
		}

		// Execute the template
		require $page_file;
		return true;
	}


	/**
	 * @inheritDoc
	 */
	public function template_exists(string $theme_base, string $path): bool {
		$page_file = $this->get_page_file($theme_base, $path);
		return is_file($page_file);
	}

	/**
	 * @inheritDoc
	 */
	public function get_supported_extensions(): array {
		return self::SUPPORTED_EXTENSIONS;
	}

	/**
	 * @inheritDoc
	 */
	public function get_type(): string {
		return 'php';
	}

	
	/**
	 * Returns the path to the page file
	 * @param string $theme_base Base path to the theme
	 * @param string $path Path to the page
	 * @return string Full path to the file
	 */
	private function get_page_file(string $theme_base, string $path): string {
		if ($path === '/') {
			return $theme_base . '/pages/index.php';
		}

		// Direct file
		$direct = $theme_base . '/pages' . $path . '.php';
		if (is_file($direct)) {
			return $direct;
		}

		// index.php in a subfolder
		$index = $theme_base . '/pages' . $path . '/index.php';
		if (is_file($index)) {
			return $index;
		}

		// Fallback
		return $direct;
	}

}
