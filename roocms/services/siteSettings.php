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



class SiteSettingsService {

    private Db $db;
    private SiteSettings $siteSettings;


    /**
     * Constructor
     */
    public function __construct(Db $db, SiteSettings $siteSettings) {
        $this->db = $db;
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


    /**
     * Update setting value
     */
    public function update_setting(string $key, mixed $value): bool {
        return $this->siteSettings->set($key, $value);
    }


    /**
     * Update multiple settings
     */
    public function update_multiple_settings(array $settings): bool {
        $success = true;

        foreach ($settings as $key => $value) {
            if (!$this->siteSettings->set($key, $value)) {
                $success = false;
            }
        }

        return $success;
    }


    /**
     * Reset setting to default value
     */
    public function reset_setting(string $key): bool {
        try {
            $this->db->update(TABLE_SETTINGS)
                ->data(['value' => NULL, 'updated_at' => time()])
                ->where('`key`', $key)
                ->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Reset all settings in a group to default values
     */
    public function reset_group_settings(string $group): bool {
        try {
            $this->db->update(TABLE_SETTINGS)
                ->data(['value' => NULL, 'updated_at' => time()])
                ->where('category', $group)
                ->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Reset all settings to default values
     */
    public function reset_all_settings(): bool {
        try {
            $this->db->update(TABLE_SETTINGS)
                ->data(['value' => NULL, 'updated_at' => time()])
                ->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Get setting metadata
     */
    public function get_setting_meta(string $key): ?array {
        return $this->siteSettings->get_meta($key);
    }


    /**
     * Check if setting exists
     */
    public function setting_exists(string $key): bool {
        return $this->siteSettings->exists($key);
    }


    /**
     * Get available groups/categories
     */
    public function get_available_groups(): array {
        $sql = "SELECT DISTINCT category FROM " . TABLE_SETTINGS . " ORDER BY category ASC";
        $results = $this->db->fetch_all($sql);
        return array_column($results, 'category');
    }
}
