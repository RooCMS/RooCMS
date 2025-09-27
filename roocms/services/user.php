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
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################



class UserService {

    private Db $db;
    private User $user;



    /**
     * Constructor
     */
    public function __construct(Db $db, User $user) {
        $this->db = $db;
        $this->user = $user;
    }


    /**
     * Get user by ID
     */
    public function get_user(int $user_id): ?array {
        return $this->user->get_user_by_id($user_id);
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
        // Normalize and validate profile fields
        foreach(['nickname', 'website'] as $field) {
            if(isset($profile_data[$field])) {
                $value = trim((string)$profile_data[$field]);
                $profile_data[$field] = $value !== '' ? $value : null;
            }
        }

        // Validate nickname uniqueness
        if(!empty($profile_data['nickname']) && $this->user->nickname_exists($profile_data['nickname'])) {
            throw new DomainException('Nickname already taken', 409);
        }

        // Validate gender
        if(isset($profile_data['gender'])) {
            $gender = $profile_data['gender'];
            $allowed_genders = ['male', 'female', 'other'];
            $profile_data['gender'] = (!empty($gender) && in_array($gender, $allowed_genders, true)) ? $gender : null;
        }

        // Validate birthday
        if(isset($profile_data['birthday'])) {
            $birthday = trim((string)$profile_data['birthday']);
            if($birthday !== '') {
                $dt = date_create_from_format('Y-m-d', $birthday);
                $errors = date_get_last_errors();
                if(!$dt || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
                    throw new DomainException('Invalid birthday format. Use Y-m-d', 422);
                }
            }
            $profile_data['birthday'] = $birthday !== '' ? $birthday : null;
        }

        // Validate website URL
        if(!empty($profile_data['website']) && !filter_var($profile_data['website'], FILTER_VALIDATE_URL)) {
            throw new DomainException('Invalid website URL', 422);
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
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new DomainException('Invalid email', 422);
            }
            // Check uniqueness of email
            $existing = $this->user->get_user_by_email($email);
            if($existing && (int)$existing['id'] !== $user_id) {
                throw new DomainException('Email already in use', 409);
            }
            $user_data['email'] = $email;
        }

        // Atomicity
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
     * Ban user until timestamp with reason; unban if until <= now
     */
    public function ban_user(int $user_id, int $until_timestamp, string $reason = ''): bool {
        $now = time();
        if($until_timestamp <= $now) {
            return $this->user->update_user($user_id, [
                'is_banned' => 0,
                'ban_expired' => 0,
                'ban_reason' => ''
            ]);
        }
        return $this->user->update_user($user_id, [
            'is_banned' => 1,
            'ban_expired' => $until_timestamp,
            'ban_reason' => $reason
        ]);
    }


    /**
     * Change email with basic validation and uniqueness check
     */
    public function change_email(int $user_id, string $new_email): bool {
        $new_email = trim($new_email);
        if(!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
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
            $this->db->delete(TABLE_TOKENS)
                ->where('user_id', $user_id)
                ->execute();

            // Clean verification codes
            $this->db->delete(TABLE_VERIFICATION_CODES)
                ->where('user_id', $user_id)
                ->execute();

            // Delete profile (will be cascaded if FK exists, but safe to delete explicitly)
            $this->db->delete(TABLE_USER_PROFILES)
                ->where('user_id', $user_id)
                ->execute();

            // Delete user
            return $this->user->delete_user($user_id);
        });
    }


    /**
     * List users (with pagination and filters)
     */
    public function get_users_list(int $page = 1, int $per_page = 20, array $filters = []): array {
        // guard page/per_page
        $page = max(1, (int)$page);
        $per_page = max(1, min(100, (int)$per_page));
        return $this->user->get_users_list($page, $per_page, $filters);
    }

    
    /**
     * Count users (with filters)
     */
    public function get_users_count(array $filters = []): int {
        return $this->user->get_users_count($filters);
    }
}