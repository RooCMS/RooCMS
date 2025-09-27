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



/**
 * Settings Controller
 * API for managing global settings
 */
class AdminSettingsController extends BaseController {

    private readonly SiteSettingsService $siteSettingsService;

    /**
     * Constructor
     */
    public function __construct(SiteSettingsService $siteSettingsService, Db|null $db = null) {
        parent::__construct($db);

        if(!$this->is_database_available()) {
            $this->error_response('Database connection required', 500);
            return;
        }

        $this->siteSettingsService = $siteSettingsService;
    }


    /**
     * Get all settings
     * GET /api/v1/admin/settings
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     */
    public function index(): void {
        $this->log_request('settings_index');

        try {
            $settings = $this->siteSettingsService->get_all_settings();
            $this->json_response($settings);
        } catch(Exception $e) {
            $this->error_response('Failed to fetch settings', 500);
        }
    }


    /**
     * Get settings by group/category
     * GET /api/v1/admin/settings/group-{group}
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param string $group Settings group
     */
    public function get_group(string $group): void {
        $this->log_request('settings_get_group', ['group' => $group]);

        try {
            $settings = $this->siteSettingsService->get_settings_by_group($group);

            if(empty($settings)) {
                $this->not_found_response('Settings group not found');
                return;
            }

            $this->json_response($settings);
        } catch(Exception $e) {
            $this->error_response('Failed to fetch settings group', 500);
        }
    }


    /**
     * Get specific setting by key
     * GET /api/v1/admin/settings/key-{key}
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param string $key Setting key
     */
    public function get_setting(string $key): void {
        $this->log_request('settings_get_setting', ['key' => $key]);

        try {
            if(!$this->siteSettingsService->setting_exists($key)) {
                $this->not_found_response('Setting not found');
                return;
            }

            $value = $this->siteSettingsService->get_setting_by_key($key);
            $meta = $this->siteSettingsService->get_setting_meta($key);

            $response = [
                'key' => $key,
                'value' => $value,
                'meta' => $meta
            ];

            $this->json_response($response);
        } catch(Exception $e) {
            $this->error_response('Failed to fetch setting', 500);
        }
    }


    /**
     * Update specific setting
     * PUT /api/v1/admin/settings/key-{key}
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param string $key Setting key
     */
    public function update_setting(string $key): void {
        $this->log_request('settings_update_setting', ['key' => $key]);

        $data = $this->get_input_data();

        if(!isset($data['value'])) {
            $this->error_response('Value field is required', 400);
            return;
        }

        try {
            if(!$this->siteSettingsService->setting_exists($key)) {
                $this->not_found_response('Setting not found');
                return;
            }

            // Get setting metadata for validation
            $meta = $this->siteSettingsService->get_setting_meta($key);
            if (!$meta) {
                $this->error_response('Setting metadata not found', 500);
                return;
            }

            // Validate value
            $validationError = $this->validate_setting_value($data['value'], $meta);
            if ($validationError) {
                $this->validation_error_response([$key => $validationError]);
                return;
            }

            if(!$this->siteSettingsService->update_setting($key, $data['value'])) {
                $this->error_response('Failed to update setting', 500);
                return;
            }

            $this->json_response(null, 200, 'Setting updated successfully');
        } catch(Exception $e) {
            $this->error_response('Failed to update setting', 500);
        }
    }


    /**
     * Partially update settings (bulk update)
     * PATCH /api/v1/admin/settings
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     */
    public function update_settings(): void {
        $this->log_request('settings_update_settings');

        $data = $this->get_input_data();
        if(empty($data) || !is_array($data)) {
            $this->error_response('Settings data is required', 400);
            return;
        }

        try {
            // Validate all settings before updating
            $validationErrors = array_filter(array_map(function($key, $value) {
                if(!$this->siteSettingsService->setting_exists($key)) {
                    return 'Setting not found';
                }
                
                $meta = $this->siteSettingsService->get_setting_meta($key);
                if (!$meta) {
                    return 'Setting metadata not found';
                }
                
                return $this->validate_setting_value($value, $meta) ?: null;
            }, array_keys($data), $data));

            if ($validationErrors) {
                $this->validation_error_response($validationErrors);
                return;
            }

            if(!$this->siteSettingsService->update_multiple_settings($data)) {
                $this->error_response('Failed to update some settings', 500);
                return;
            }

            $this->json_response(null, 200, 'Settings updated successfully');
        } catch(Exception $e) {
            $this->error_response('Failed to update settings', 500);
        }
    }


    /**
     * Validate setting value according to its metadata
     *
     * @param mixed $value Value to validate
     * @param array $meta Setting metadata
     * @return string|null Validation error message or null if valid
     */
    private function validate_setting_value(mixed $value, array $meta): ?string {
        // Normalize and check empty values
        $value = $value ?? '';
        $isEmpty = $value === '';
        
        if ($isEmpty) {
            return $meta['is_required'] ? 'This field is required' : null;
        }

        // Check maximum length for string types
        if ($meta['max_length'] && is_string($value) && strlen($value) > $meta['max_length']) {
            return "Value exceeds maximum length of {$meta['max_length']} characters";
        }

        // Validate by type (only for non-empty values)
        return match ($meta['type']) {
            'boolean' => $this->validate_boolean_value($value),
            'integer' => $this->validate_integer_value($value),
            'string', 'text', 'html', 'color' => $this->validate_string_value($value),
            'date' => $this->validate_date_value($value),
            'email' => $this->validate_email_value($value),
            'select' => $this->validate_select_value($value, $meta),
            'image', 'file' => $this->validate_string_value($value),
            default => null
        };
    }


    /**
     * Validate boolean value
     * @param mixed $value Value
     * @return string|null Validation error message or null if valid
     */
    private function validate_boolean_value(mixed $value): ?string {
        if (!is_bool($value) && !in_array($value, [0, 1, '0', '1'], true)) {
            return 'Value must be a boolean (true/false, 0/1)';
        }
        return null;
    }


    /**
     * Validate integer value
     * @param mixed $value Value
     * @return string|null Validation error message or null if valid
     */
    private function validate_integer_value(mixed $value): ?string {
        if (!is_numeric($value) || !is_int($value + 0)) {
            return 'Value must be an integer';
        }
        return null;
    }


    /**
     * Validate string value
     * @param mixed $value Value
     * @return string|null Validation error message or null if valid
     */
    private function validate_string_value(mixed $value): ?string {
        if (!is_string($value)) {
            return 'Value must be a string';
        }
        return null;
    }


    /**
     * Validate date value
     * @param mixed $value Value
     * @return string|null Validation error message or null if valid
     */
    private function validate_date_value(mixed $value): ?string {
        if (!is_numeric($value) || $value <= 0) {
            return 'Value must be a valid timestamp';
        }
        return null;
    }


    /**
     * Validate email value
     * @param mixed $value Value
     * @return string|null Validation error message or null if valid
     */
    private function validate_email_value(mixed $value): ?string {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return 'Value must be a valid email address';
        }
        return null;
    }


    /**
     * Validate select value
     * @param mixed $value Value
     * @param array $meta Setting metadata
     * @return string|null Validation error message or null if valid
     */
    private function validate_select_value(mixed $value, array $meta): ?string {
        // Check for empty values
        $isEmpty = in_array($value, [null, '', 0, '0'], true);
        if ($isEmpty) {
            return $meta['is_required'] ? 'This field is required' : null;
        }

        // Select fields must have options defined
        if (empty($meta['options'])) {
            return 'Select field must have options defined';
        }

        // Check if value exists in options (convert to string for comparison)
        if (!array_key_exists((string)$value, $meta['options'])) {
            return 'Value must be one of: ' . implode(', ', array_keys($meta['options']));
        }

        return null;
    }

    /**
     * Reset specific setting to default value
     * GET /api/v1/admin/settings/reset/key-{key}
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param string $key Setting key
     */
    public function reset_setting(string $key): void {
        $this->log_request('settings_reset_setting', ['key' => $key]);

        try {
            if(!$this->siteSettingsService->setting_exists($key)) {
                $this->not_found_response('Setting not found');
                return;
            }

            if(!$this->siteSettingsService->reset_setting($key)) {
                $this->error_response('Failed to reset setting', 500);
                return;
            }

            $this->json_response(null, 200, 'Setting reset to default value');
        } catch(Exception $e) {
            $this->error_response('Failed to reset setting', 500);
        }
    }


    /**
     * Reset all settings in a group to default values
     * GET /api/v1/admin/settings/reset/group-{group}
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param string $group Settings group
     */
    public function reset_group(string $group): void {
        $this->log_request('settings_reset_group', ['group' => $group]);

        try {
            // Check if group exists by getting settings
            $settings = $this->siteSettingsService->get_settings_by_group($group);

            if(empty($settings)) {
                $this->not_found_response('Settings group not found');
                return;
            }

            if(!$this->siteSettingsService->reset_group_settings($group)) {
                $this->error_response('Failed to reset group settings', 500);
                return;
            }

            $this->json_response(null, 200, 'Group settings reset to default values');
        } catch(Exception $e) {
            $this->error_response('Failed to reset group settings', 500);
        }
    }


    /**
     * Reset all settings to default values
     * GET /api/v1/admin/settings/reset/all
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     */
    public function reset_all(): void {
        $this->log_request('settings_reset_all');

        try {
            if(!$this->siteSettingsService->reset_all_settings()) {
                $this->error_response('Failed to reset all settings', 500);
                return;
            }

            $this->json_response(null, 200, 'All settings reset to default values');
        } catch(Exception $e) {
            $this->error_response('Failed to reset all settings', 500);
        }
    }
}
