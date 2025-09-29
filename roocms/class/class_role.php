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
 * Role Class
 * Provides utilities for working with user roles
 */
class Role {

    // Role constants
    public const USER = 'u';
    public const MODERATOR = 'm';
    public const ADMIN = 'a';
    public const SUPERUSER = 'su';

    // Role names
    private const ROLE_NAMES = [
        self::USER => 'User',
        self::MODERATOR => 'Moderator',
        self::ADMIN => 'Administrator',
        self::SUPERUSER => 'Superuser'
    ];

    // Role descriptions
    private const ROLE_DESCRIPTIONS = [
        self::USER => 'Typical user of the site',
        self::MODERATOR => 'Moderator with content management rights',
        self::ADMIN => 'Administrator with full access rights',
        self::SUPERUSER => 'Superuser with unlimited rights'
    ];

    // Role hierarchy levels (higher number = more permissions)
    private const ROLE_LEVELS = [
        self::USER => 1,
        self::MODERATOR => 2,
        self::ADMIN => 3,
        self::SUPERUSER => 4
    ];



    /**
     * Get role name
     * 
     * @param string $role Role
     * @return string
     */
    public function get_role_name(string $role): string {
        return self::ROLE_NAMES[$role] ?? 'Unknown role';
    }


    /**
     * Get role description
     * 
     * @param string $role Role
     * @return string
     */
    public function get_role_description(string $role): string {
        return self::ROLE_DESCRIPTIONS[$role] ?? 'Role description is not available';
    }


    /**
     * Get role hierarchy level
     * 
     * @param string $role Role
     * @return int
     */
    public function get_role_level(string $role): int {
        return self::ROLE_LEVELS[$role] ?? 0;
    }


    /**
     * Get all available roles
     * 
     * @return array
     */
    public function get_all_roles(): array {
        return [
            self::USER,
            self::MODERATOR,
            self::ADMIN,
            self::SUPERUSER
        ];
    }


    /**
     * Get roles list with names and descriptions
     * 
     * @return array
     */
    public function get_roles_list(): array {
        $roles = [];
        foreach ($this->get_all_roles() as $role_key) {
            $roles[$role_key] = [
                'key' => $role_key,
                'name' => $this->get_role_name($role_key),
                'description' => $this->get_role_description($role_key),
                'level' => $this->get_role_level($role_key)
            ];
        }
        return $roles;
    }


    /**
     * Check if role has admin access (moderator, admin, superuser)
     * 
     * @param string $role Role
     * @return bool
     */
    public function has_admin_access(string $role): bool {
        return in_array($role, [self::ADMIN, self::SUPERUSER]);
    }


    /**
     * Check if role has moderator access or higher
     * 
     * @param string $role Role
     * @return bool
     */
    public function has_moderator_access(string $role): bool {
        return in_array($role, [self::MODERATOR, self::ADMIN, self::SUPERUSER]);
    }


    /**
     * Check if role is superuser
     * 
     * @param string $role Role
     * @return bool
     */
    public function is_superuser(string $role): bool {
        return $role === self::SUPERUSER;
    }


    /**
     * Check if user role has sufficient level
     * 
     * @param string $user_role User role
     * @param int $required_level Required level
     * @return bool
     */
    public function has_sufficient_level(string $user_role, int $required_level): bool {
        $user_level = $this->get_role_level($user_role);
        return $user_level >= $required_level;
    }


    /**
     * Check if user role can access specific role's permissions
     * 
     * @param string $user_role User role
     * @param string $target_role Target role
     * @return bool
     */
    public function can_access_role(string $user_role, string $target_role): bool {
        $user_level = $this->get_role_level($user_role);
        $target_level = $this->get_role_level($target_role);
        return $user_level >= $target_level;
    }


    /**
     * Get default role for new users
     * 
     * @return string
     */
    public function get_default_role(): string {
        return self::USER;
    }


    /**
     * Validate role key
     * 
     * @param string $role Role
     * @return bool
     */
    public function is_valid_role(string $role): bool {
        return in_array($role, $this->get_all_roles());
    }


    /**
     * Format role for display
     * 
     * @param string $role Role
     * @return array
     */
    public function format_role(string $role): array {
        return [
            'key' => $role,
            'name' => $this->get_role_name($role),
            'description' => $this->get_role_description($role),
            'level' => $this->get_role_level($role),
            'has_admin_access' => $this->has_admin_access($role),
            'is_superuser' => $this->is_superuser($role)
        ];
    }
}
