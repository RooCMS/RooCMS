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
 * StructureCommonService
 * Common methods for working with site structure
 * Contains common methods for working with site structure
 */
trait StructureCommonService {


    /**
     * Check if page is published
     * 
     * @param array $page Page data
     * @return bool True if published
     */
    private function is_page_published(array $page): bool {
        // Check status
        if ($page['status'] !== 'active') {
            return false;
        }

        // Check published_at timestamp
        $published_at = (int)($page['published_at'] ?? 0);
        if ($published_at > 0 && $published_at > time()) {
            return false; // Published in the future
        }

        return true;
    }
}