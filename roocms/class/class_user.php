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




class User {

    private Db $db;
    private Role $role;


    /**
     * Constructor
     * 
     * @param Db $db Database
     */
    public function __construct(Db $db, Role $role) {
        $this->db = $db;
        $this->role = $role;
    }


    /**
     * Get user by ID
     * 
     * @param int $user_id User ID
     * @return array|null
     */
    public function get_user_by_id(int $user_id): ?array {
        return $this->get_user_by('id', $user_id, false);
    }


    /**
     * Get user by login
     * 
     * @param string $login Login
     * @return array|null
     */
    public function get_user_by_login(string $login): ?array {
        return $this->get_user_by('login', $login, true);
    }


    /**
     * Get user by email
     * 
     * @param string $email Email
     * @return array|null
     */
    public function get_user_by_email(string $email): ?array {
        return $this->get_user_by('email', $email, false);
    }


    /**
     * Base fetcher by arbitrary field
     * 
     * @param string $field Field
     * @param int|string $value Value
     * @param bool $with_password With password
     * @return array|null
     */
    private function get_user_by(string $field, int|string $value, bool $with_password = false): ?array {
        try {
            $password_column = $with_password ? ', u.password' : '';
            $query = "SELECT 
                        u.id, u.role, u.is_active, u.login, u.email, u.is_verified, u.is_banned, 
                        u.ban_expired, u.ban_reason, u.created_at, u.updated_at, u.last_activity{$password_column},
                        u.is_deleted, u.deleted_at,
                        p.nickname, p.first_name, p.last_name, p.gender, p.avatar, p.bio, 
                        p.birthday, p.website, p.is_public
                      FROM " . TABLE_USERS . " u
                      LEFT JOIN " . TABLE_USER_PROFILES . " p ON u.id = p.user_id
                      WHERE u." . $field . " = ? AND u.is_deleted = 0 LIMIT 1";

            $row = $this->db->fetch_assoc($query, [$value]);

            if($row !== false) {
                $row['role_name'] = $this->role->get_role_name($row['role']);
                $row['role_description'] = $this->role->get_role_description($row['role']);
                $row['role_level'] = $this->role->get_role_level($row['role']);
                return $row;
            }
            
            return null;
        } catch (Exception $e) {
            error_log('Error getting user by ' . $field . ': ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Create new user
     * 
     * @param array $user_data User data
     * @return int|bool
     */
    public function create_user(array $user_data): int|bool {
        try {
            $current_time = time();
            
            $query = "INSERT INTO " . TABLE_USERS . " 
                     (role, is_active, is_verified, is_banned, ban_expired, ban_reason, 
                      login, email, password, created_at, updated_at, last_activity) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $user_data['role'] ?? 'u',
                $user_data['is_active'] ?? 0,
                $user_data['is_verified'] ?? 0,
                $user_data['is_banned'] ?? 0,
                $user_data['ban_expired'] ?? $current_time,
                $user_data['ban_reason'] ?? '',
                $user_data['login'],
                $user_data['email'],
                $user_data['password'],
                $current_time,
                $current_time,
                $user_data['last_activity'] ?? $current_time
            ];
            
            $result = $this->db->query($query, $params);
            
            if ($result->rowCount() > 0) {
                return (int)$this->db->insert_id();
            }
            
            return false;
        } catch (Exception $e) {
            error_log('Error creating user: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Update user
     * 
     * @param int $user_id User ID
     * @param array $user_data User data
     * @return bool
     */
    public function update_user(int $user_id, array $user_data): bool {
        try {
            $set_clauses = [];
            $params = [];
            
            $allowed_fields = ['role', 'is_active', 'email', 'is_verified', 'is_banned', 'ban_expired', 'ban_reason', 'password'];
            
            foreach ($allowed_fields as $field) {
                if (array_key_exists($field, $user_data)) {
                    $set_clauses[] = "{$field} = ?";
                    $params[] = $user_data[$field];
                }
            }
            
            if (empty($set_clauses)) {
                return false;
            }
            
            $set_clauses[] = "updated_at = ?";
            $params[] = time();
            $params[] = $user_id;
            
            $query = "UPDATE " . TABLE_USERS . " SET " . implode(', ', $set_clauses) . " WHERE id = ?";
            
            $this->db->query($query, $params);
            return true;
        } catch (Exception $e) {
            error_log('Error updating user: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Delete user (soft delete)
     * 
     * @param int $user_id User ID
     * @return bool
     */
    public function delete_user(int $user_id): bool {
        try {
            $query = "UPDATE " . TABLE_USERS . " SET is_deleted = 1, deleted_at = ?, updated_at = ? WHERE id = ?";
            $current_time = time();
            $this->db->query($query, [$current_time, $current_time, $user_id]);
            return true;
        } catch (Exception $e) {
            error_log('Error deleting user: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Create or update user profile
     *
     * @param int $user_id User ID
     * @param array $profile_data Profile data
     * @return bool
     */
    public function upsert_profile(int $user_id, array $profile_data): bool {
        try {
            $current_time = time();
            $allowed_fields = ['nickname', 'first_name', 'last_name', 'gender', 'avatar', 'bio', 'birthday', 'website', 'is_public'];

            // Decide update or insert
            if($this->db->check_id($user_id, TABLE_USER_PROFILES, 'user_id')) {
                // Update: filter only provided and allowed fields
                $update_data = array_intersect_key($profile_data, array_flip($allowed_fields));
                
                if(empty($update_data)) {
                    return true; // No fields to update
                }

                $update_data['updated_at'] = $current_time;
                return $this->db->update_array($update_data, TABLE_USER_PROFILES, 'user_id = ?', [$user_id]);
            }

            // Insert: prepare data with defaults for missing fields
            $insert_data = [
                'user_id' => $user_id,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ];

            // Add provided fields or defaults
            foreach($allowed_fields as $field) {
                $insert_data[$field] = $profile_data[$field] ?? ($field === 'is_public' ? 0 : null);
            }

            return $this->db->insert_array($insert_data, TABLE_USER_PROFILES);
        } catch (Exception $e) {
            error_log('Error upserting user profile: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Get user profile
     * 
     * @param int $user_id User ID
     * @return array|null
     */
    public function get_profile(int $user_id): ?array {
        try {
            $query = "SELECT * FROM " . TABLE_USER_PROFILES . " WHERE user_id = ? LIMIT 1";
            $row = $this->db->fetch_assoc($query, [$user_id]);
            return $row !== false ? $row : null;
        } catch (Exception $e) {
            error_log('Error getting user profile: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Check if user exists by login
     * 
     * @param string $login Login
     * @return bool
     */
    public function login_exists(string $login): bool {
        try {
            $query = "SELECT COUNT(*) as count FROM " . TABLE_USERS . " WHERE login = ? AND is_deleted = 0";
            $result = $this->db->fetch_assoc($query, [$login]);
            return $result && $result['count'] > 0;
        } catch (Exception $e) {
            error_log('Error checking login exists: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Check if user exists by email
     * 
     * @param string $email Email
     * @return bool
    */
    public function email_exists(string $email): bool {
        try {
            $query = "SELECT COUNT(*) as count FROM " . TABLE_USERS . " WHERE email = ? AND is_deleted = 0";
            $result = $this->db->fetch_assoc($query, [$email]);
            return $result && $result['count'] > 0;
        } catch (Exception $e) {
            error_log('Error checking email exists: ' . $e->getMessage());
            return false;
        }
    }


    /**
    * Check if user exists by nickname
     * 
     * @param string $nickname Nickname
     * @param int|null $exclude_user_id User ID to exclude from check
     * @return bool
    */
    public function nickname_exists(string $nickname, ?int $exclude_user_id = null): bool {
        try {
            if ($exclude_user_id !== null) {
                $query = "SELECT COUNT(*) as count FROM " . TABLE_USER_PROFILES . " WHERE nickname = ? AND user_id != ?";
                $result = $this->db->fetch_assoc($query, [$nickname, $exclude_user_id]);
            } else {
                $query = "SELECT COUNT(*) as count FROM " . TABLE_USER_PROFILES . " WHERE nickname = ?";
                $result = $this->db->fetch_assoc($query, [$nickname]);
            }
            return $result && $result['count'] > 0;
        } catch (Exception $e) {
            error_log('Error checking nickname exists: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Update last activity
     * 
     * @param int $user_id User ID
     * @return bool
     */
    public function update_last_activity(int $user_id): bool {
        try {
            $query = "UPDATE " . TABLE_USERS . " SET last_activity = ? WHERE id = ?";
            $this->db->query($query, [time(), $user_id]);
            return true;
        } catch (Exception $e) {
            error_log('Error updating last activity: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Get users list with pagination
     * 
     * @param int $page Page
     * @param int $per_page Per page
     * @param array $filters Filters
     * @return array
     */
    public function get_users_list(int $page = 1, int $per_page = 20, array $filters = []): array {
        try {
            $offset = ($page - 1) * $per_page;
            $where_conditions = [];
            $params = [];

            // Default filter: only non-deleted users
            $filters['is_deleted'] = $filters['is_deleted'] ?? 0;

            // Build filters using simple mapping
            $field_filters = [
                'role' => !empty($filters['role']),
                'is_active' => isset($filters['is_active']),
                'is_banned' => isset($filters['is_banned']),
                'is_deleted' => isset($filters['is_deleted'])
            ];

            foreach ($field_filters as $field => $should_add) {
                if ($should_add) {
                    $where_conditions[] = "u.{$field} = ?";
                    $params[] = $filters[$field];
                }
            }

            // Search filter
            if (!empty($filters['search'])) {
                $where_conditions[] = "(u.login LIKE ? OR u.email LIKE ? OR p.nickname LIKE ? OR p.first_name LIKE ? OR p.last_name LIKE ?)";
                $search_term = '%' . $filters['search'] . '%';
                $params = array_merge($params, array_fill(0, 5, $search_term));
            }

            $where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

            $query = "SELECT 
                        u.id, u.role, u.is_active, u.login, u.email, u.is_verified, u.is_banned,
                        u.created_at, u.updated_at, u.last_activity,
                        p.nickname, p.first_name, p.last_name, p.avatar, p.is_public
                      FROM " . TABLE_USERS . " u
                      LEFT JOIN " . TABLE_USER_PROFILES . " p ON u.id = p.user_id
                      {$where_clause}
                      ORDER BY u.created_at DESC
                      LIMIT ? OFFSET ?";

            $params[] = $per_page;
            $params[] = $offset;

            return $this->db->fetch_all($query, $params) ?: [];
        } catch (Exception $e) {
            error_log('Error getting users list: ' . $e->getMessage());
            return [];
        }
    }

    
    /**
     * Get users count
     * 
     * @param array $filters Filters
     * @return int
     */
    public function get_users_count(array $filters = []): int {
        try {
            // Set default filter for non-deleted users
            $filters['is_deleted'] = $filters['is_deleted'] ?? 0;

            // Define filter mappings for cleaner code (same as get_users_list)
            $filter_mappings = [
                'role' => fn($value) => ["u.role = ?", $value],
                'is_active' => fn($value) => ["u.is_active = ?", $value],
                'is_banned' => fn($value) => ["u.is_banned = ?", $value],
                'is_deleted' => fn($value) => ["u.is_deleted = ?", $value],
            ];

            $where_conditions = [];
            $params = [];

            // Build WHERE conditions using mappings (same logic as get_users_list)
            foreach ($filter_mappings as $key => $mapper) {
                if (isset($filters[$key]) && ($key !== 'role' ? $filters[$key] !== null : !empty($filters[$key]))) {
                    [$condition, $value] = $mapper($filters[$key]);
                    $where_conditions[] = $condition;
                    $params[] = $value;
                }
            }

            // Handle search filter separately
            if (!empty($filters['search'])) {
                $where_conditions[] = "(u.login LIKE ? OR u.email LIKE ? OR p.nickname LIKE ? OR p.first_name LIKE ? OR p.last_name LIKE ?)";
                $search_term = '%' . $filters['search'] . '%';
                $params = array_merge($params, array_fill(0, 5, $search_term));
            }

            $where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

            $query = "SELECT COUNT(*) as total
                      FROM " . TABLE_USERS . " u
                      LEFT JOIN " . TABLE_USER_PROFILES . " p ON u.id = p.user_id
                      {$where_clause}";

            $result = $this->db->fetch_assoc($query, $params);
            return $result ? (int) $result['total'] : 0;
        } catch (Exception $e) {
            error_log('Error getting users count: ' . $e->getMessage());
            return 0;
        }
    }
}