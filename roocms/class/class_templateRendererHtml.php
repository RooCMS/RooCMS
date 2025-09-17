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
 * Class TemplateRendererHtml
 * Renderer for HTML templates with support for placeholders and includes
 */
class TemplateRendererHtml implements TemplateRenderer {

	private const SUPPORTED_EXTENSIONS = ['html', 'htm'];  	// Supported extensions
	private array $global_vars = []; 						// Global variables
	private const MAX_INCLUDES = 10; 						// Maximum number of includes



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
		$layout_file = $theme_base . '/layouts/base.html';

		if (!$this->template_exists($theme_base, $path) || !is_file($layout_file)) {
			return false;
		}

		// Load files
		$layout = file_read($layout_file);
		$content = file_read($page_file);

		if ($layout === false || $content === false) {
			return false;
		}

		// Parse metadata from the content
		$meta = $this->parse_meta_data($content);

		// Process includes in the content
		$content = $this->process_includes($content, $theme_base);

		// Process assets
		$content = $this->process_assets($content, $theme_base);

		// Prepare variables for replacement
		$variables = $this->prepare_variables($meta, $data, $theme_base);

		// Process conditional blocks
		$content = $this->process_conditionals($content, $variables);

		// Process loops
		$content = $this->process_loops($content, $variables);

		// Clean metadata from the content
		$content = $this->clean_meta_data($content);

		// Replace variables in the content without HTML escaping
		$content = $this->replace_variables_in_content($content, $variables);

		// Load and process partials
		$partials = $this->load_partials($theme_base, $variables);

		// Assemble the final HTML
		$final_html = $this->assemble_html($layout, $content, $partials, $variables);

		// Send headers and output
		header('Content-Type: text/html; charset=utf-8');
		echo $final_html;

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
		return 'html';
	}


	/**
	 * Returns the path to the page file
	 * @param string $theme_base Base path to the theme
	 * @param string $path Path to the page
	 * @return string Full path to the file
	 */
	private function get_page_file(string $theme_base, string $path): string {
		if ($path === '/') {
			return $theme_base . '/pages/index.html';
		}

		// First try to find a file with the path name
		$direct_file = $theme_base . '/pages' . $path . '.html';
		if (is_file($direct_file)) {
			return $direct_file;
		}

		// If the file is not found, try to find index.html in a subfolder
		$index_file = $theme_base . '/pages' . $path . '/index.html';
		if (is_file($index_file)) {
			return $index_file;
		}

		// Fallback - return the direct path
		return $direct_file;
	}


	/**
	 * Parses metadata from HTML comments
	 * @param string $content Content of the page
	 * @return array Metadata
	 */
	private function parse_meta_data(string $content): array {
		$meta = [
			'title' => 'RooCMS',
			'description' => 'RooCMS website',
			'scripts' => [],
			'vars' => []
		];

		// JSON format: <!-- meta: { ... } -->
		if (preg_match('/<!--\s*meta:\s*(\{.*?\})\s*-->/s', $content, $matches)) {
			$decoded = json_decode($matches[1], true);
			if (is_array($decoded)) {
				$meta = array_merge($meta, $decoded);
			}
		}


		return $meta;
	}


	/**
	 * Processes includes in the content
	 * @param string $content Content
	 * @param string $theme_base Base path to the theme
	 * @return string Processed content
	 */
	private function process_includes(string $content, string $theme_base): string {
		$include_pattern = '/<!--\s*include:\s*([^\s]+)\s*-->/';
		$iterations = 0;

		while ($iterations < self::MAX_INCLUDES && preg_match($include_pattern, $content, $matches)) {
			$include_path = trim($matches[1]);
			$include_file = $theme_base . '/' . ltrim($include_path, '/');

			$replacement = '';
			if (is_file($include_file)) {
				$replacement = file_read($include_file) ?: '';
			}

			$content = preg_replace($include_pattern, $replacement, $content, 1);
			$iterations++;
		}

		return $content;
	}


	/**
	 * Processes assets
	 * @param string $content Content
	 * @param string $theme_base Base path to the theme
	 * @return string Processed content
	 */
	private function process_assets(string $content, string $theme_base): string {
		$theme_web = '/themes/' . basename($theme_base);

		$content = preg_replace_callback(
			'/{{\s*asset:\s*([^}]+)\s*}}/',
			function($matches) use ($theme_web) {
				$path = trim($matches[1]);
				return $theme_web . '/assets/' . ltrim($path, '/');
			},
			$content
		);

		return $content;
	}


	/**
	 * Prepares variables for replacement
	 * @param array $meta Metadata
	 * @param array $data Page data
	 * @param string $theme_base Base path to the theme
	 * @return array Variables
	 */
	private function prepare_variables(array $meta, array $data, string $theme_base): array {
		$theme_web = '/themes/' . basename($theme_base);

		$variables = array_merge($this->global_vars, $data);

		// Standard variables
		$variables = array_merge($variables, [
			'title' => $meta['title'],
			'description' => $meta['description'],
			'theme_base' => $theme_web,
			'year' => date('Y'),
			'content' => '', // will be set later
			'header' => '',
			'footer' => '',
			'page_scripts' => ''
		]);


		// Variables from meta.vars
		if (!empty($meta['vars']) && is_array($meta['vars'])) {
			foreach ($meta['vars'] as $key => $value) {
				$variables[$key] = $value;
			}
		}


		// Scripts
		if (!empty($meta['scripts'])) {
			$scripts = is_array($meta['scripts']) ? $meta['scripts'] : [$meta['scripts']];
			$script_tags = [];
			foreach ($scripts as $script) {
				$script = trim($script);
				if ($script !== '') {
					$resolved = (strpos($script, '/') === 0) ? $script : ($theme_web . '/' . ltrim($script, '/'));
					$script_tags[] = '<script type="module" src="' . htmlspecialchars($resolved, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"></script>';
				}
			}
			$variables['page_scripts'] = implode("\n\t", $script_tags);
		}

		return $variables;
	}


	/**
	 * Processes conditional blocks
	 * @param string $content Content
	 * @param array $variables Variables
	 * @return string Processed content
	 */
	private function process_conditionals(string $content, array $variables): string {
		// <!-- if: variable --> content <!-- endif -->
		$content = preg_replace_callback(
			'/<!--\s*if:\s*(\w+)\s*-->(.*?)<!--\s*endif\s*-->/s',
			function($matches) use ($variables) {
				$var = $matches[1];
				$content_block = $matches[2];

				if (isset($variables[$var]) && !empty($variables[$var])) {
					return $content_block;
				}

				return '';
			},
			$content
		);

		return $content;
	}


	/**
	 * Processes loops
	 * @param string $content Content
	 * @param array $variables Variables
	 * @return string Processed content
	 */
	private function process_loops(string $content, array $variables): string {
		// <!-- foreach: items as item --> content <!-- endforeach -->
		$content = preg_replace_callback(
			'/<!--\s*foreach:\s*(\w+)\s+as\s+(\w+)\s*-->(.*?)<!--\s*endforeach\s*-->/s',
			function($matches) use ($variables) {
				$array_var = $matches[1];
				$item_var = $matches[2];
				$template = $matches[3];

				if (!isset($variables[$array_var]) || !is_array($variables[$array_var])) {
					return '';
				}

				$result = '';
				foreach ($variables[$array_var] as $item) {
					$item_content = str_replace('{{' . $item_var . '}}', htmlspecialchars((string)$item, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $template);
					$result .= $item_content;
				}

				return $result;
			},
			$content
		);

		return $content;
	}


	/**
	 * Cleans metadata from the content
	 * @param string $content Content
	 * @return string Cleaned content
	 */
	private function clean_meta_data(string $content): string {
		return preg_replace('/<!--\s*meta:\s*\{.*?\}\s*-->/s', '', $content);
	}


	/**
	 * Replaces variables in the content without HTML escaping
	 * @param string $content Content
	 * @param array $variables Variables
	 * @return string Processed content
	 */
	private function replace_variables_in_content(string $content, array $variables): string {
		foreach ($variables as $key => $value) {
			if (is_scalar($value)) {
				// {{{variable}}} for raw output
				$content = str_replace('{{{' . $key . '}}}', (string)$value, $content);
				// {{variable}} for normal output (without HTML escaping for HTML content)
				$content = str_replace('{{' . $key . '}}', (string)$value, $content);
			}
		}

		return $content;
	}


	/**
	 * Replaces variables in the content with HTML escaping
	 * @param string $content Content
	 * @param array $variables Variables
	 * @return string Processed content
	 */
	private function replace_variables(string $content, array $variables): string {
		foreach ($variables as $key => $value) {
			if (is_scalar($value)) {
				// {{{variable}}} for raw output
				$content = str_replace('{{{' . $key . '}}}', (string)$value, $content);
				// {{variable}} for escaped output
				$content = str_replace('{{' . $key . '}}', htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $content);
			}
		}

		return $content;
	}


	/**
	 * Loads partials
	 * @param string $theme_base Base path to the theme
	 * @param array $variables Variables
	 * @return array Partials
	 */
	private function load_partials(string $theme_base, array &$variables): array {
		$partials = [];

		$header_file = $theme_base . '/partials/header.html';
		if (is_file($header_file)) {
			$header_content = file_read($header_file);
			if ($header_content !== false) {
				$variables['header'] = $this->replace_variables_in_content($header_content, $variables);
			}
		}

		$footer_file = $theme_base . '/partials/footer.html';
		if (is_file($footer_file)) {
			$footer_content = file_read($footer_file);
			if ($footer_content !== false) {
				$variables['footer'] = $this->replace_variables_in_content($footer_content, $variables);
			}
		}

		return $partials;
	}

	
	/**
	 * Assembles the final HTML
	 * @param string $layout Layout
	 * @param string $content Content
	 * @param array $partials Partials
	 * @param array $variables Variables
	 * @return string Final HTML
	 */
	private function assemble_html(string $layout, string $content, array $partials, array $variables): string {
		$variables['content'] = $content;

		$final_html = $layout;

		// Variables that do not need to be escaped (already contain processed HTML)
		$no_escape_vars = ['header', 'footer', 'content', 'page_scripts'];

		foreach ($variables as $key => $value) {
			if (is_scalar($value)) {
				if (in_array($key, $no_escape_vars)) {
					$final_html = str_replace('{{' . $key . '}}', (string)$value, $final_html);
				} else {
					$final_html = str_replace('{{' . $key . '}}', htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $final_html);
				}
			}
		}

		return $final_html;
	}
}
