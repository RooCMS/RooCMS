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




class User {

    private Db $db;


    /**
     * Constructor
     * 
     * @param Db $db Database
     */
    public function __construct(Db $db) {
        $this->db = $db;
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
                        p.nickname, p.first_name, p.last_name, p.gender, p.avatar, p.bio, 
                        p.birthday, p.website, p.is_public
                      FROM " . TABLE_USERS . " u
                      LEFT JOIN " . TABLE_USER_PROFILES . " p ON u.id = p.user_id
                      WHERE u." . $field . " = ? LIMIT 1";

            $row = $this->db->fetch_assoc($query, [$value]);
            return $row !== false ? $row : null;
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
                     (role, is_active, login, email, password, created_at, updated_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $user_data['role'] ?? 'u',
                $user_data['is_active'] ?? 0,
                $user_data['login'],
                $user_data['email'],
                $user_data['password'],
                $current_time,
                $current_time
            ];
            
            $result = $this->db->query($query, $params);
            
            if ($result) {
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
     * Delete user
     * 
     * @param int $user_id User ID
     * @return bool
     */
    public function delete_user(int $user_id): bool {
        try {
            $query = "DELETE FROM " . TABLE_USERS . " WHERE id = ?";
            $this->db->query($query, [$user_id]);
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

            // Decide update or insert
            $exists = $this->db->check_id($user_id, TABLE_USER_PROFILES, 'user_id');
            if($exists) {
                // Update only provided fields
                $set_clauses = [];
                $params = [];

                // Build SET clauses only for provided fields
                if (array_key_exists('nickname', $profile_data)) {
                    $set_clauses[] = "nickname = ?";
                    $params[] = $profile_data['nickname'];
                }
                if (array_key_exists('first_name', $profile_data)) {
                    $set_clauses[] = "first_name = ?";
                    $params[] = $profile_data['first_name'];
                }
                if (array_key_exists('last_name', $profile_data)) {
                    $set_clauses[] = "last_name = ?";
                    $params[] = $profile_data['last_name'];
                }
                if (array_key_exists('gender', $profile_data)) {
                    $set_clauses[] = "gender = ?";
                    $params[] = $profile_data['gender'];
                }
                if (array_key_exists('avatar', $profile_data)) {
                    $set_clauses[] = "avatar = ?";
                    $params[] = $profile_data['avatar'];
                }
                if (array_key_exists('bio', $profile_data)) {
                    $set_clauses[] = "bio = ?";
                    $params[] = $profile_data['bio'];
                }
                if (array_key_exists('birthday', $profile_data)) {
                    $set_clauses[] = "birthday = ?";
                    $params[] = $profile_data['birthday'];
                }
                if (array_key_exists('website', $profile_data)) {
                    $set_clauses[] = "website = ?";
                    $params[] = $profile_data['website'];
                }
                if (array_key_exists('is_public', $profile_data)) {
                    $set_clauses[] = "is_public = ?";
                    $params[] = $profile_data['is_public'];
                }

                if (empty($set_clauses)) {
                    return true; // No fields to update
                }

                $set_clauses[] = "updated_at = ?";
                $params[] = $current_time;
                $params[] = $user_id;

                $query = "UPDATE " . TABLE_USER_PROFILES . " SET " . implode(', ', $set_clauses) . " WHERE user_id = ?";
                return (bool)$this->db->query($query, $params);
            }

            // Insert new profile - use provided data with defaults for missing fields
            $data = [
                'user_id' => $user_id,
                'nickname' => $profile_data['nickname'] ?? null,
                'first_name' => $profile_data['first_name'] ?? null,
                'last_name' => $profile_data['last_name'] ?? null,
                'gender' => $profile_data['gender'] ?? null,
                'avatar' => $profile_data['avatar'] ?? null,
                'bio' => $profile_data['bio'] ?? null,
                'birthday' => $profile_data['birthday'] ?? null,
                'website' => $profile_data['website'] ?? null,
                'is_public' => $profile_data['is_public'] ?? 1,
                'created_at' => $current_time,
                'updated_at' => $current_time,
            ];

            return $this->db->insert_array($data, TABLE_USER_PROFILES);
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
            $query = "SELECT COUNT(*) as count FROM " . TABLE_USERS . " WHERE login = ?";
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
            $query = "SELECT COUNT(*) as count FROM " . TABLE_USERS . " WHERE email = ?";
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
     * @return bool
    */
    public function nickname_exists(string $nickname): bool {
        try {
            $query = "SELECT COUNT(*) as count FROM " . TABLE_USER_PROFILES . " WHERE nickname = ?";
            $result = $this->db->fetch_assoc($query, [$nickname]);
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

            // Filters
            if (!empty($filters['role'])) {
                $where_conditions[] = "u.role = ?";
                $params[] = $filters['role'];
            }

            if (isset($filters['is_active'])) {
                $where_conditions[] = "u.is_active = ?";
                $params[] = $filters['is_active'];
            }

            if (isset($filters['is_banned'])) {
                $where_conditions[] = "u.is_banned = ?";
                $params[] = $filters['is_banned'];
            }

            if (!empty($filters['search'])) {
                $where_conditions[] = "(u.login LIKE ? OR u.email LIKE ? OR p.nickname LIKE ? OR p.first_name LIKE ? OR p.last_name LIKE ?)";
                $search_term = '%' . $filters['search'] . '%';
                $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term, $search_term]);
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
            $where_conditions = [];
            $params = [];

            // Filters (the same as in get_users_list)
            if (!empty($filters['role'])) {
                $where_conditions[] = "u.role = ?";
                $params[] = $filters['role'];
            }

            if (isset($filters['is_active'])) {
                $where_conditions[] = "u.is_active = ?";
                $params[] = $filters['is_active'];
            }

            if (isset($filters['is_banned'])) {
                $where_conditions[] = "u.is_banned = ?";
                $params[] = $filters['is_banned'];
            }

            if (!empty($filters['search'])) {
                $where_conditions[] = "(u.login LIKE ? OR u.email LIKE ? OR p.nickname LIKE ? OR p.first_name LIKE ? OR p.last_name LIKE ?)";
                $search_term = '%' . $filters['search'] . '%';
                $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term, $search_term]);
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