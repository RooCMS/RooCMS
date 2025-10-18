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
 * UserManageService
 * Service layer for admin user management operations
 * Contains business logic for full user control by administrators
 */
class UserManageService {

    private Db $db;
    private User $user;
    private UserValidationService $userValidationService;
    private Role $role;



    /**
     * Constructor
     */
    public function __construct(Db $db, User $user, UserValidationService $userValidationService, Role $role) {
        $this->db = $db;
        $this->user = $user;
        $this->userValidationService = $userValidationService;
        $this->role = $role;
    }


    /**
     * Get user by ID with detailed information for admin
     * 
     * @param int $user_id User ID
     * @return array|null User data or null if not found
     */
    public function get_user_for_edit(int $user_id): ?array {
        return $this->user->get_user_by_id($user_id, true);
    }


    /**
     * Update user account and profile (admin version with full control)
     * 
     * @param int $user_id User ID
     * @param array $data Combined user and profile data
     * @return bool Success
     * @throws DomainException If validation fails
     */
    public function update_user_full(int $user_id, array $data): bool {
        // Check if user exists
        $existing_user = $this->user->get_user_by_id($user_id);
        if(!$existing_user) {
            throw new DomainException('User not found', 404);
        }

        // Separate user and profile data
        $user_fields = ['email', 'role', 'is_active', 'is_verified', 'is_banned', 'ban_expired', 'ban_reason'];
        $profile_fields = ['nickname', 'first_name', 'last_name', 'gender', 'avatar', 'bio', 'birthday', 'website', 'is_public'];

        $user_updates = array_intersect_key($data, array_flip($user_fields));
        $profile_updates = array_intersect_key($data, array_flip($profile_fields));

        // Validate and process user updates
        if(!empty($user_updates)) {
            // Validate email if changed
            if(isset($user_updates['email'])) {
                $email = trim((string)$user_updates['email']);
                $this->userValidationService->validate_email($email, $user_id);
                $user_updates['email'] = $email;
            }

            // Validate role if changed
            if(isset($user_updates['role'])) {
                $role = trim((string)$user_updates['role']);
                $this->userValidationService->validate_role($role);
                $user_updates['role'] = $role;
            }

            // Normalize boolean fields
            foreach(['is_active', 'is_verified', 'is_banned'] as $bool_field) {
                if(isset($user_updates[$bool_field])) {
                    $user_updates[$bool_field] = (int)(bool)$user_updates[$bool_field];
                }
            }

            // Validate ban_expired if set
            if(isset($user_updates['ban_expired'])) {
                $ban_expired = (int)$user_updates['ban_expired'];
                $this->userValidationService->validate_ban_expiration($ban_expired);
                $user_updates['ban_expired'] = $ban_expired;
            }

            // Normalize ban_reason
            if(isset($user_updates['ban_reason'])) {
                $user_updates['ban_reason'] = trim((string)$user_updates['ban_reason']);
            }
        }

        // Validate profile updates using UserValidationService
        if(!empty($profile_updates)) {
            // Normalize string fields to null if empty
            $string_fields = ['nickname', 'website', 'birthday', 'gender'];
            foreach($string_fields as $field) {
                if(isset($profile_updates[$field])) {
                    $value = trim((string)$profile_updates[$field]);
                    $profile_updates[$field] = $value !== '' ? $value : null;
                }
            }

            // Normalize gender field to lowercase if it's not null
            if(isset($profile_updates['gender']) && $profile_updates['gender'] !== null) {
                $profile_updates['gender'] = strtolower((string)$profile_updates['gender']);
            }

            // Validate specific fields using UserValidationService
            if(isset($profile_updates['nickname']) && !empty($profile_updates['nickname'])) {
                $this->userValidationService->validate_nickname($profile_updates['nickname'], $user_id);
            }

            if(isset($profile_updates['gender'])) {
                $this->userValidationService->validate_gender($profile_updates['gender']);
            }

            if(isset($profile_updates['birthday'])) {
                $this->userValidationService->validate_birthday($profile_updates['birthday']);
            }

            if(isset($profile_updates['website'])) {
                $this->userValidationService->validate_website($profile_updates['website']);
            }

            // Normalize boolean field
            if(isset($profile_updates['is_public'])) {
                $profile_updates['is_public'] = (int)(bool)$profile_updates['is_public'];
            }
        }

        // Execute updates in transaction
        return (bool)$this->db->transaction(function() use ($user_id, $user_updates, $profile_updates) {
            $success = true;
            
            if(!empty($user_updates)) {
                $success = $success && $this->user->update_user($user_id, $user_updates);
            }
            
            if(!empty($profile_updates)) {
                $success = $success && $this->user->upsert_profile($user_id, $profile_updates);
            }
            
            return $success;
        });
    }


    /**
     * Change user password (admin version - no current password required)
     * 
     * @param int $user_id User ID
     * @param string $new_password New password
     * @return bool Success
     * @throws DomainException If validation fails
     */
    public function change_user_password(int $user_id, string $new_password): bool {
        // Check if user exists
        $existing_user = $this->user->get_user_by_id($user_id);
        if(!$existing_user) {
            throw new DomainException('User not found', 404);
        }

        // Validate password strength
        $this->userValidationService->validate_password_strength($new_password);

        // Hash password
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        if($password_hash === false) {
            throw new DomainException('Failed to hash password', 500);
        }

        // Update password
        return $this->user->update_user($user_id, ['password' => $password_hash]);
    }


    /**
     * Change user role
     * 
     * @param int $user_id User ID
     * @param string $new_role New role (u, m, a, su)
     * @return bool Success
     * @throws DomainException If validation fails
     */
    public function change_user_role(int $user_id, string $new_role): bool {
        // Validate role
        $this->userValidationService->validate_role($new_role);

        // Check if user exists
        $existing_user = $this->user->get_user_by_id($user_id);
        if(!$existing_user) {
            throw new DomainException('User not found', 404);
        }

        return $this->user->update_user($user_id, ['role' => $new_role]);
    }


    /**
     * Activate user account
     * 
     * @param int $user_id User ID
     * @return bool Success
     */
    public function activate_user(int $user_id): bool {
        return $this->user->update_user($user_id, ['is_active' => 1]);
    }


    /**
     * Deactivate user account
     * 
     * @param int $user_id User ID
     * @return bool Success
     */
    public function deactivate_user(int $user_id): bool {
        return $this->user->update_user($user_id, ['is_active' => 0]);
    }


    /**
     * Verify user email (admin version)
     * 
     * @param int $user_id User ID
     * @return bool Success
     */
    public function verify_user_email(int $user_id): bool {
        return $this->user->update_user($user_id, ['is_verified' => 1]);
    }


    /**
     * Unverify user email (admin version)
     * 
     * @param int $user_id User ID
     * @return bool Success
     */
    public function unverify_user_email(int $user_id): bool {
        return $this->user->update_user($user_id, ['is_verified' => 0]);
    }




    /**
     * Delete user account (soft delete with cleanup)
     * 
     * @param int $user_id User ID
     * @return bool Success
     */
    public function delete_user(int $user_id): bool {
        // Check if user exists
        $existing_user = $this->user->get_user_by_id($user_id);
        if(!$existing_user) {
            throw new DomainException('User not found', 404);
        }

        return (bool)$this->db->transaction(function() use ($user_id) {
            // Clean tokens
            $this->db->query('DELETE FROM ' . TABLE_TOKENS . ' WHERE user_id = ?', [$user_id]);

            // Clean verification codes
            $this->db->query('DELETE FROM ' . TABLE_VERIFICATION_CODES . ' WHERE user_id = ?', [$user_id]);

            // Soft delete user
            return $this->user->delete_user($user_id);
        });
    }


    /**
     * Get available roles with descriptions
     * 
     * @return array Roles list
     */
    public function get_available_roles(): array {
        return [
            ['value' => 'u', 'label' => 'User', 'description' => 'Regular user'],
            ['value' => 'm', 'label' => 'Moderator', 'description' => 'Can moderate content'],
            ['value' => 'a', 'label' => 'Admin', 'description' => 'Can manage site settings'],
            ['value' => 'su', 'label' => 'Superuser', 'description' => 'Full system access']
        ];
    }
}

