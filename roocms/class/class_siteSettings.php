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
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################



class SiteSettings {

    private Db $db;



    /**
     * Constructor class
     * @param Db $db Db object
     */
    public function __construct(Db $db) {
        $this->db = $db;

        static $initialized = false;
        if (!$initialized) {
            $initialized = true;
            $roocms_settings = $this->get_by_category('roocms');
            foreach ($roocms_settings as $key => $value) {
                //TODO: change to internal public variables
                define('RooCMS_'.$key, $value);
            }
        }
    }


    /**
     * Get setting value by key
     * @param string $key Setting key
     * @return mixed Setting value or default_value if not found
     */
    public function get_by_key(string $key): mixed {
        $sql = "
            SELECT value, default_value, is_serialized
            FROM " . TABLE_SETTINGS . "
            WHERE `key` = ?
        ";
        $result = $this->db->fetch_assoc($sql, [$key]);

        if (!$result) {
            return null;
        }

        $value = $result['value'] ?? $result['default_value'];

        // Deserialization if necessary
        if ($result['is_serialized'] && $value !== null) {
            $value = unserialize($value);
        }

        return $value;
    }


    /**
     * Get all settings by category
     * @param string $category Category of settings
     * @return array Array of settings [key => value]
     */
    public function get_by_category(string $category): array {
        $sql = "
            SELECT `key`, value, default_value, is_serialized
            FROM " . TABLE_SETTINGS . "
            WHERE category = ?
            ORDER BY sort_order ASC, title ASC
        ";
        $results = $this->db->fetch_all($sql, [$category]);

        $settings = [];
        foreach ($results as $row) {
            $value = $row['value'] ?? $row['default_value'];

            // Deserialization if necessary
            if ($row['is_serialized'] && $value !== null) {
                $value = unserialize($row['value']);
            }

            $settings[$row['key']] = $value;
        }

        return $settings;
    }


    /**
     * Get all settings
     * @return array Array of all settings [category][key => value]
     */
    public function get_all(): array {
        $sql = "
            SELECT category, `key`, value, default_value, is_serialized
            FROM " . TABLE_SETTINGS . "
            ORDER BY category ASC, sort_order ASC, title ASC
        ";
        $results = $this->db->fetch_all($sql);

        $settings = [];
        foreach ($results as $row) {
            $value = $row['value'] ?? $row['default_value'];

            // Deserialization if necessary
            if ($row['is_serialized'] && $value !== null) {
                $value = unserialize($row['value']);
            }

            if (!isset($settings[$row['category']])) {
                $settings[$row['category']] = [];
            }

            $settings[$row['category']][$row['key']] = $value;
        }

        return $settings;
    }


    /**
     * Save setting value
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool Success of operation
     */
    public function set(string $key, mixed $value): bool {
        try {
            // Get setting information
            $sql = "
                SELECT type, is_serialized, max_length, is_required, options
                FROM " . TABLE_SETTINGS . "
                WHERE `key` = ?
            ";
            $setting = $this->db->fetch_assoc($sql, [$key]);

            if (!$setting) {
                return false;
            }

            // Decode options if they exist
            if ($setting['options']) {
                $setting['options'] = json_decode($setting['options'], true);
            }

            // Validate value
            if (!$this->validate_value($value, $setting)) {
                return false;
            }

            // Serialization if necessary
            $serializedValue = $setting['is_serialized'] ? serialize($value) : $value;

            // Update value
            $data = [
                'value' => $serializedValue,
                'updated_at' => time()
            ];

            return $this->db->update_array($data, TABLE_SETTINGS, "`key` = ?", [$key]);

        } catch (Exception $e) {
            return false;
        }
    }


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

            return $result ? $this->db->insert_id() : false;

        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Delete setting
     * @param string $key Setting key
     * @return bool Success of operation
     */
    public function delete(string $key): bool {
        try {
            $sql = "DELETE FROM " . TABLE_SETTINGS . " WHERE `key` = ?";
            $stmt = $this->db->query($sql, [$key]);
            return $stmt !== false;
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Check if setting exists
     * @param string $key Setting key
     * @return bool Does setting exist
     */
    public function exists(string $key): bool {
        $sql = "SELECT 1 FROM " . TABLE_SETTINGS . " WHERE `key` = ?";
        return (bool) $this->db->fetch_column($sql, [$key]);
    }


    /**
     * Get setting metadata
     * @param string $key Setting key
     * @return array|null Setting metadata
     */
    public function get_meta(string $key): ?array {
        $sql = "
            SELECT id, category, sort_order, title, description, type, options, max_length, is_required, is_serialized, created_at, updated_at
            FROM " . TABLE_SETTINGS . "
            WHERE `key` = ?
        ";
        $result = $this->db->fetch_assoc($sql, [$key]);

        if ($result && $result['options']) {
            $result['options'] = json_decode($result['options'], true);
        }

        return $result ?: null;
    }


    /**
     * Get available values for select setting
     * @param string $key Setting key
     * @return array Array of available values [value => label] or empty array
     */
    public function get_select_options(string $key): array {
        $meta = $this->get_meta($key);

        if (!$meta || $meta['type'] !== 'select' || empty($meta['options'])) {
            return [];
        }

        return $meta['options'];
    }


    /**
     * Check if value is valid for select setting
     * @param mixed $value Value to check
     * @param array $setting Setting metadata
     * @return bool Is value valid
     */
    private function validate_select_value(mixed $value, array $setting): bool {
        // First check if value is a string
        if (!is_string($value)) {
            return false;
        }

        // If options are not set, then any string value is valid
        if (empty($setting['options'])) {
            return true;
        }

        // Check if value is in the list of valid options
        $options = $setting['options'];

        // If options is a JSON string (from database), decode it
        if (is_string($options)) {
            $options = json_decode($options, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return false; // Invalid JSON
            }
        }

        // Check if options is an array
        if (!is_array($options)) {
            return false;
        }

        // Check if value is in the keys of the options array
        return array_key_exists($value, $options);
    }


    /**
     * Validate setting value
     * @param mixed $value Value
     * @param array $setting Setting metadata
     * @return bool Is value valid
     */
    private function validate_value(mixed $value, array $setting): bool {
        // Check if required
        if ($setting['is_required'] && ($value === null || $value === '')) {
            return false;
        }

        // Check maximum length for string types
        if ($setting['max_length'] && is_string($value) && strlen($value) > $setting['max_length']) {
            return false;
        }

        // Validate by type
        return match ($setting['type']) {
            'boolean' => is_bool($value) || in_array($value, [0, 1, '0', '1']),
            'integer' => is_numeric($value) && is_int($value + 0),
            'string', 'text', 'html', 'color' => is_string($value),
            'date' => is_numeric($value) && $value > 0,
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'select' => $this->validate_select_value($value, $setting),
            'image', 'file' => is_string($value),
            default => true
        };
    }
}