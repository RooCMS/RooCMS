<?php declare(strict_types=1);
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 */

//#########################################################
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################


/**
 * Trait TemplatePathResolver
 * Common path resolution logic for template renderers
 */
trait TemplatePathResolver {

	/**
	 * Resolves the file path based on the route and file extension
	 * @param string $theme_base Base path to the theme
	 * @param string $path Request path
	 * @param string $extension File extension (.php, .html, etc.)
	 * @return string Full path to the file
	 */
	protected function resolve_path(string $theme_base, string $path, string $extension): string {
		// Check if path starts with '/!/' - system routes
		if (str_starts_with($path, '/!/')) {
			$system_path = substr($path, 2); // Remove '/!' prefix, keep the rest

			// Direct system file
			$direct_system = $theme_base . '/system' . $system_path . $extension;
			if (is_file($direct_system)) {
				return $direct_system;
			}

			// index file in system subfolder
			$index_system = $theme_base . '/system' . $system_path . '/index' . $extension;
			if (is_file($index_system)) {
				return $index_system;
			}

			// Fallback to direct system file
			return $direct_system;
		}

		// Root page
		if ($path === '/') {
			return $theme_base . '/pages/index' . $extension;
		}

		// Direct file in pages
		$direct = $theme_base . '/pages' . $path . $extension;
		if (is_file($direct)) {
			return $direct;
		}

		// index file in subfolder
		$index = $theme_base . '/pages' . $path . '/index' . $extension;
		if (is_file($index)) {
			return $index;
		}

		// Fallback
		return $direct;
	}
}
