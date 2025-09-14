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

// Define root path
defined('_SITEROOT') or define('_SITEROOT', __DIR__);

// Bootstrap RooCMS
require_once _SITEROOT.'/roocms/init.php';

// Detect active theme (from settings or fallback to 'default')
$active_theme = defined('RooCMS_active_theme') && RooCMS_active_theme
    ? (string)RooCMS_active_theme
    : 'default';

$theme_base = _SITEROOT.'/themes/'.$active_theme;

// Basic router: map URI path to page file under theme
$uri = env('REQUEST_URI') ?? '/';
$path = sanitize_path($uri);
if ($path === false) {
    http_response_code(400);
    exit('Bad Request');
}

// Strip query string already done by sanitize_path; normalize trailing slash
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

// Map well-known paths
$routes = [
    '/'                 => $theme_base.'/pages/index.php',
    '/auth/login'       => $theme_base.'/pages/auth/login.php',
    '/users'            => $theme_base.'/pages/users/index.php',
    '/users/me'         => $theme_base.'/pages/users/me.php',
];

$page_file = $routes[$path] ?? null;

// Fallback: try direct mapping under theme pages (e.g., /about -> pages/about.php)
if ($page_file === null) {
    $candidate = $theme_base.'/pages'.$path.'.php';
    if (is_file($candidate)) {
        $page_file = $candidate;
    }
}

// 404 fallback
if ($page_file === null || !is_file($page_file)) {
    http_response_code(404);
    $page_file = $theme_base.'/pages/404.php';
    if (!is_file($page_file)) {
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html><head><meta charset="utf-8"><title>404</title></head><body><h1>404 Not Found</h1></body></html>';
        exit;
    }
}

// Render page
require $page_file;


