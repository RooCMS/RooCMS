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

/**
 * include bootstrap
 */
require_once 'bootstrap.php';

//#########################################################
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################


/**
 * Register template renderers and themes
 */
$container->register(TemplateRendererPhp::class, TemplateRendererPhp::class, true);
$container->register(TemplateRendererHtml::class, TemplateRendererHtml::class, true);
$container->register(Themes::class, function(DependencyContainer $c) {
	// Inject renderers via DI, themes dir defaults to 'themes'
	return new Themes(
		$c->get(TemplateRendererPhp::class),
		$c->get(TemplateRendererHtml::class),
		'themes'
	);
}, true);
