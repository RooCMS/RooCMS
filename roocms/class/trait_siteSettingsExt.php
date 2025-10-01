<?php
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
 * Trait for operations Site Settings Ext
 */
trait SiteSettingsExt {


    /**
     * Create new setting
     * @param array $data Setting data
     * @return bool|int ID of created setting or false on error
     */
    public function create(array $data): bool|int {
        try {
            $options = isset($data['options']) ? json_encode($data['options']) : null;
            $value = isset($data['value']) ? $data['value'] : null;
            $default_value = isset($data['default_value']) ? $data['default_value'] : null;

            // Serialization values if necessary
            if ($data['is_serialized'] ?? false) {
                if ($value !== null) $value = serialize($value);
                if ($default_value !== null) $default_value = serialize($default_value);
            }

            $insertData = [
                'category' => $data['category'] ?? 'general',
                'sort_order' => $data['sort_order'] ?? 1,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'key' => $data['key'],
                'type' => $data['type'] ?? 'string',
                'options' => $options,
                'value' => $value,
                'default_value' => $default_value,
                'max_length' => $data['max_length'] ?? null,
                'is_required' => $data['is_required'] ?? 0,
                'is_serialized' => $data['is_serialized'] ?? 0,
                'created_at' => time(),
                'updated_at' => time()
            ];

            $result = $this->db->insert_array($insertData, TABLE_SETTINGS);

            return $result ? (int)$this->db->insert_id() : false;

        } catch (Exception $e) {
            return false;
        }
    }
}