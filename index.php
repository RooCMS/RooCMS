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
//  RooCMS Front Controller for Theme Rendering
//#########################################################

//#########################################################
//  TODO: This is Draft
//#########################################################

const RooCMS = true;

/**
 * define root roocms path
 */
if(!defined('_SITEROOT')) {
    define('_SITEROOT', __DIR__);
}

// Bootstrap RooCMS
require_once _SITEROOT.'/roocms/init.php';

// Detect active theme (from settings or fallback to 'default')
$active_theme = $site['theme'] ?? 'default';

// Parse URI path
$uri = env('REQUEST_URI') ?? '/';
$path = sanitize_path($uri);
if ($path === false) {
    http_response_code(400);
    exit('Bad Request');
}

// Normalize trailing slash
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// Initialize theme system and set active theme from DI container
/** @var DependencyContainer $container */
$themes = $container->get(Themes::class);
if (!$themes->set_theme($active_theme)) {
    // Fallback to default theme
    $themes->set_theme('default');
}

// Try to render the page
if (!$themes->render($path)) {
    // Ensure HTTP 404 status for missing page
    http_response_code(404);
    // Try themed 404 page
    if (!$themes->render('/404')) {
        // Raw 404 if no 404 template
        output_html('<!doctype html><html><head><meta charset="utf-8"><title>404</title></head><body><h1>404 Not Found</h1></body></html>');
    }
}