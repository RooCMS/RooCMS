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
 * SiteSettingsService
 * Service layer for working with site settings
 * Contains business logic, validation, and error handling
 */
class SiteSettingsService {

    private SiteSettings $siteSettings;

    public function __construct(SiteSettings $siteSettings) {
        $this->siteSettings = $siteSettings;
    }


    /**
     * Get all settings
     */
    public function get_all_settings(): array {
        return $this->siteSettings->get_all();
    }


    /**
     * Get settings by category/group
     */
    public function get_settings_by_group(string $group): array {
        return $this->siteSettings->get_by_category($group);
    }


    /**
     * Get setting by key
     */
    public function get_setting_by_key(string $key): mixed {
        return $this->siteSettings->get_by_key($key);
    }
}