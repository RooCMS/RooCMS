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



class UserService {

    private Db $db;
    private User $user;
    private Files $files;



    /**
     * Constructor
     */
    public function __construct(Db $db, User $user, Files $files) {
        $this->db = $db;
        $this->user = $user;
        $this->files = $files;
    }


    /**
     * Get user by ID
     * 
     * @param int $user_id User ID
     * @param array|null $caller Caller user (null if not authenticated)
     * @return array|null User data or null if not found/no access
     */
    public function get_user(int $user_id, ?array $caller = null): ?array {
        // Determine if full access should be granted
        $has_full_access = false;
        
        if($caller) {
            // User requesting their own profile
            if((int)$caller['id'] === $user_id) {
                $has_full_access = true;
            }
            // Admin or superuser requesting any profile
            elseif(in_array($caller['role'] ?? '', ['a', 'su'], true)) {
                $has_full_access = true;
            }
        }
        
        // Get user data based on access level
        if($has_full_access) {
            // Full access: include detailed data
            $user = $this->user->get_user_by_id($user_id, true);
        } else {
            // Limited access: only basic data
            $user = $this->user->get_user_by_id($user_id, false);
        }

        if(!$user) {
            return null;
        }
        
        return $user;
    }


    /**
     * Get user profile by ID
     */
    public function get_profile(int $user_id): ?array {
        return $this->user->get_profile($user_id);
    }


    /**
     * Create or update user profile by ID
     */
    public function upsert_profile(int $user_id, array $profile_data): bool {
        // Normalize string fields to null if empty
        $string_fields = ['nickname', 'website', 'birthday', 'gender'];
        foreach($string_fields as $field) {
            if(isset($profile_data[$field])) {
                $value = trim((string)$profile_data[$field]);
                $profile_data[$field] = $value !== '' ? $value : null;
            }
        }

        // Normalize gender field to lowercase if it's not null
        if(isset($profile_data['gender']) && $profile_data['gender'] !== null) {
            $profile_data['gender'] = strtolower((string)$profile_data['gender']);
        }

        // Validate specific fields
        $validations = [
            'nickname' => fn($v) => !empty($v) && $this->user->nickname_exists($v, $user_id) ?
                throw new DomainException('Nickname already taken', 409) : null,
            'gender' => fn($v) => $v !== null && !in_array($v, ['male', 'female', 'other'], true) ?
                throw new DomainException('Invalid gender value. Must be one of: male, female, other', 422) : null,
            'birthday' => fn($v) => !empty($v) && (!($dt = date_create_from_format('Y-m-d', $v)) || ($errors = date_get_last_errors()) && $errors['error_count'] > 0) ? 
                throw new DomainException('Invalid birthday format. Use Y-m-d', 422) : null,
            'website' => fn($v) => !empty($v) && !filter_var($v, FILTER_VALIDATE_URL) ? 
                throw new DomainException('Invalid website URL', 422) : null,
        ];

        foreach($validations as $field => $validator) {
            if(isset($profile_data[$field])) {
                $validator($profile_data[$field]);
            }
        }

        // Normalize boolean field
        if(isset($profile_data['is_public'])) {
            $profile_data['is_public'] = (int)(bool)$profile_data['is_public'];
        }

        // Atomicity: in one transaction
        return (bool)$this->db->transaction(function() use ($user_id, $profile_data) {
            return $this->user->upsert_profile($user_id, $profile_data);
        });
    }


    /**
     * Update user fields by ID
     */
    public function update_user(int $user_id, array $user_data): bool {
        // Protect immutable fields and cast types
        unset($user_data['id'], $user_data['login']);

        if(isset($user_data['email'])) {
            $email = trim((string)$user_data['email']);
            if(!is_valid_email($email)) {
                throw new DomainException('Invalid email', 422);
            }
            // Check uniqueness of email
            $existing = $this->user->get_user_by_email($email);
            if($existing && (int)$existing['id'] !== $user_id) {
                throw new DomainException('Email already in use', 409);
            }
            $user_data['email'] = $email;
        }

        return (bool)$this->db->transaction(function() use ($user_id, $user_data) {
            return $this->user->update_user($user_id, $user_data);
        });
    }


    /**
     * Activate/deactivate user account by ID
     */
    public function set_active(int $user_id, bool $is_active): bool {
        return $this->user->update_user($user_id, ['is_active' => $is_active ? 1 : 0]);
    }


    /**
     * Verify/unverify user by ID
     */
    public function set_verified(int $user_id, bool $is_verified): bool {
        return $this->user->update_user($user_id, ['is_verified' => $is_verified ? 1 : 0]);
    }


    /**
     * Change email with basic validation and uniqueness check
     */
    public function change_email(int $user_id, string $new_email): bool {
        $new_email = trim($new_email);
        if(!is_valid_email($new_email)) {
            throw new DomainException('Invalid email', 422);
        }
        $existing = $this->user->get_user_by_email($new_email);
        if($existing && (int)$existing['id'] !== $user_id) {
            throw new DomainException('Email already in use', 409);
        }
        return $this->user->update_user($user_id, ['email' => $new_email]);
    }


    /**
     * Delete user with related tokens and verification codes
     */
    public function delete_user(int $user_id): bool {
        return (bool)$this->db->transaction(function() use ($user_id) {
            // Clean tokens
            $this->db->query('DELETE FROM ' . TABLE_TOKENS . ' WHERE user_id = ?', [$user_id]);

            // Clean verification codes
            $this->db->query('DELETE FROM ' . TABLE_VERIFICATION_CODES . ' WHERE user_id = ?', [$user_id]);

            // Delete profile (will be cascaded if FK exists, but safe to delete explicitly)
            $this->db->query('DELETE FROM ' . TABLE_USER_PROFILES . ' WHERE user_id = ?', [$user_id]);

            // Delete user
            return $this->user->delete_user($user_id);
        });
    }


    /**
     * Upload avatar for user
     * 
     * @param array $file Uploaded file from $_FILES
     * @param int $user_id User ID
     * @return string Avatar file path
     * @throws DomainException
     */
    public function upload_avatar(array $file, int $user_id): string {
        
        // Validate basic file requirements using Files class
        $file_info = $this->files->validate_uploaded_file($file);
        
        // Validate that it's an image
        if($file_info['media_type'] !== 'image') {
            throw new DomainException('Avatar must be an image file', 400);
        }
        
        // Validate file size (max 5MB for avatars)
        $max_size = 5 * 1024 * 1024; // 5MB
        if($file['size'] > $max_size) {
            throw new DomainException('Avatar file size must not exceed 5MB', 413);
        }
        
        // Validate MIME type for images
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if(!in_array($file_info['mime_type'], $allowed_mimes, true)) {
            throw new DomainException('Avatar must be JPEG, PNG, GIF or WebP image', 415);
        }
        
        // Delete old avatar if exists
        $this->delete_avatar($user_id);
        
        // Generate unique filename and path
        $extension = $file_info['extension'];
        $filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
        $avatar_dir = _UPLOADAV . '/';
        $avatar_path = $avatar_dir . $filename;
        
        // Ensure avatar directory exists
        if(!is_dir($avatar_dir)) {
            mkdir($avatar_dir, 0755, true);
        }
        
        // Move uploaded file
        if(!move_uploaded_file($file['tmp_name'], $avatar_path)) {
            throw new DomainException('Failed to save avatar file', 500);
        }
        
        // Process avatar using Files class (resize to reasonable size, no thumbnails)
        $filename_without_ext = pathinfo($filename, PATHINFO_FILENAME);
        
        // Ensure filename is string (pathinfo can return mixed types in edge cases)
        if(!is_string($filename_without_ext)) {
            throw new DomainException('Invalid filename for avatar processing', 500);
        }
        
        $this->process_avatar_image($avatar_path, $filename_without_ext, $extension);
        
        // Update user profile with avatar path (relative to upload directory)
        $relative_path = 'av/' . $filename;
        $success = $this->user->upsert_profile($user_id, ['avatar' => $relative_path]);
        
        if(!$success) {
            // Clean up file if database update failed
            @unlink($avatar_path);
            throw new DomainException('Failed to update user profile with avatar', 500);
        }
        
        return $relative_path;
    }


    /**
     * Delete avatar for user
     * 
     * @param int $user_id User ID
     * @return bool Success
     */
    public function delete_avatar(int $user_id): bool {
        
        // Get current user profile
        $profile = $this->user->get_profile($user_id);
        
        if(!$profile || empty($profile['avatar'])) {
            return false; // No avatar to delete
        }
        
        // Delete physical file
        $avatar_path = _UPLOAD . '/' . $profile['avatar'];
        if(file_exists($avatar_path)) {
            $deleted = @unlink($avatar_path);
            if(!$deleted) {
                error_log("Failed to delete avatar file: " . $avatar_path);
            }
        }
        
        // Update profile to remove avatar
        return $this->user->upsert_profile($user_id, ['avatar' => null]);
    }


    /**
     * Process avatar image using Files class functionality
     * 
     * @param string $file_path Full path to avatar file
     * @param string $filename Filename without extension
     * @param string $extension File extension
     * @return void
     */
    private function process_avatar_image(string $file_path, string $filename, string $extension): void {
        
        // Use Files class to process image (resize to 400x400, no watermark for avatars, force resize)
        $success = $this->files->process_image($file_path, $filename, $extension, 400, 400, false, true);
        
        if(!$success) {
            // Log error but don't throw exception - leave original file
            error_log("Avatar processing failed for file: " . $file_path);
        }
    }
}