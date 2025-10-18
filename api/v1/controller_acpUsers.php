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
 * ACP Users Controller
 * API for managing users with full administrative control
 */
class ACPUsersController extends BaseController {

    private readonly UserManageService $userManageService;
    private readonly UserService $userService;


    /**
     * Constructor
     */
    public function __construct(Db $db, Request $request, UserManageService $userManageService, UserService $userService) {
        parent::__construct($db, $request);

        $this->userManageService = $userManageService;
        $this->userService = $userService;
    }


    /**
     * Get user for editing with full details
     * GET /api/v1/acp/users/{user_id}
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param int $user_id User ID
     */
    public function show(int $user_id): void {
        $this->log_request('admin_users_show', ['user_id' => $user_id]);

        try {
            $user = $this->userManageService->get_user_for_edit($user_id);

            if(!$user) {
                $this->not_found_response('User not found');
                return;
            }

            $this->json_response($user);
        } catch(Exception $e) {
            $this->error_response('Failed to fetch user', 500);
        }
    }


    /**
     * Update user with full control (account + profile)
     * PUT /api/v1/acp/users/{user_id}
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param int $user_id User ID
     */
    public function update(int $user_id): void {
        $this->log_request('admin_users_update', ['user_id' => $user_id]);

        // Get input data
        $data = $this->get_input_data();
        
        if(empty($data)) {
            $this->error_response('No data provided for update', 400);
            return;
        }

        try {
            $success = $this->userManageService->update_user_full($user_id, $data);

            if(!$success) {
                $this->error_response('Failed to update user', 500);
                return;
            }

            $this->json_response(null, 200, 'User updated successfully');
        } catch(DomainException $e) {
            $this->error_response($e->getMessage(), $e->getCode() ?: 400);
        } catch(Exception $e) {
            $this->error_response('Failed to update user', 500);
        }
    }


    /**
     * Change user password (admin version - no current password required)
     * PUT /api/v1/acp/users/{user_id}/password
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param int $user_id User ID
     */
    public function change_password(int $user_id): void {
        $this->log_request('admin_users_change_password', ['user_id' => $user_id]);

        // Get input data
        $data = $this->get_input_data();

        // Validate required fields
        if(!isset($data['password'])) {
            $this->error_response('Password field is required', 400);
            return;
        }

        try {
            $success = $this->userManageService->change_user_password($user_id, (string)$data['password']);

            if(!$success) {
                $this->error_response('Failed to change password', 500);
                return;
            }

            $this->json_response(null, 200, 'Password changed successfully');
        } catch(DomainException $e) {
            $this->error_response($e->getMessage(), $e->getCode() ?: 400);
        } catch(Exception $e) {
            $this->error_response('Failed to change password', 500);
        }
    }


    /**
     * Change user role
     * PATCH /api/v1/acp/users/{user_id}/role
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param int $user_id User ID
     */
    public function change_role(int $user_id): void {
        $this->log_request('admin_users_change_role', ['user_id' => $user_id]);

        // Get input data
        $data = $this->get_input_data();

        // Validate required fields
        if(!isset($data['role'])) {
            $this->error_response('Role field is required', 400);
            return;
        }

        try {
            $success = $this->userManageService->change_user_role($user_id, (string)$data['role']);

            if(!$success) {
                $this->error_response('Failed to change role', 500);
                return;
            }

            $this->json_response(null, 200, 'Role changed successfully');
        } catch(DomainException $e) {
            $this->error_response($e->getMessage(), $e->getCode() ?: 400);
        } catch(Exception $e) {
            $this->error_response('Failed to change role', 500);
        }
    }


    /**
     * Activate user account
     * PATCH /api/v1/acp/users/{user_id}/activate
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param int $user_id User ID
     */
    public function activate(int $user_id): void {
        $this->log_request('admin_users_activate', ['user_id' => $user_id]);

        try {
            $success = $this->userManageService->activate_user($user_id);

            if(!$success) {
                $this->error_response('Failed to activate user', 500);
                return;
            }

            $this->json_response(null, 200, 'User activated successfully');
        } catch(Exception $e) {
            $this->error_response('Failed to activate user', 500);
        }
    }


    /**
     * Deactivate user account
     * PATCH /api/v1/acp/users/{user_id}/deactivate
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param int $user_id User ID
     */
    public function deactivate(int $user_id): void {
        $this->log_request('admin_users_deactivate', ['user_id' => $user_id]);

        try {
            $success = $this->userManageService->deactivate_user($user_id);

            if(!$success) {
                $this->error_response('Failed to deactivate user', 500);
                return;
            }

            $this->json_response(null, 200, 'User deactivated successfully');
        } catch(Exception $e) {
            $this->error_response('Failed to deactivate user', 500);
        }
    }


    /**
     * Verify user email (admin version)
     * PATCH /api/v1/acp/users/{user_id}/verify
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param int $user_id User ID
     */
    public function verify(int $user_id): void {
        $this->log_request('admin_users_verify', ['user_id' => $user_id]);

        try {
            $success = $this->userManageService->verify_user_email($user_id);

            if(!$success) {
                $this->error_response('Failed to verify user email', 500);
                return;
            }

            $this->json_response(null, 200, 'User email verified successfully');
        } catch(Exception $e) {
            $this->error_response('Failed to verify user email', 500);
        }
    }


    /**
     * Unverify user email (admin version)
     * PATCH /api/v1/acp/users/{user_id}/unverify
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param int $user_id User ID
     */
    public function unverify(int $user_id): void {
        $this->log_request('admin_users_unverify', ['user_id' => $user_id]);

        try {
            $success = $this->userManageService->unverify_user_email($user_id);

            if(!$success) {
                $this->error_response('Failed to unverify user email', 500);
                return;
            }

            $this->json_response(null, 200, 'User email unverified successfully');
        } catch(Exception $e) {
            $this->error_response('Failed to unverify user email', 500);
        }
    }


    /**
     * Delete user (admin version)
     * DELETE /api/v1/acp/users/{user_id}
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     *
     * @param int $user_id User ID
     */
    public function delete(int $user_id): void {
        $this->log_request('admin_users_delete', ['user_id' => $user_id]);

        try {
            $success = $this->userManageService->delete_user($user_id);

            if(!$success) {
                $this->error_response('Failed to delete user', 500);
                return;
            }

            $this->json_response(null, 200, 'User deleted successfully');
        } catch(DomainException $e) {
            $this->error_response($e->getMessage(), $e->getCode() ?: 400);
        } catch(Exception $e) {
            $this->error_response('Failed to delete user', 500);
        }
    }


    /**
     * Get available roles
     * GET /api/v1/acp/users/roles
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     */
    public function roles(): void {
        $this->log_request('admin_users_roles');

        try {
            $roles = $this->userManageService->get_available_roles();
            $this->json_response($roles);
        } catch(Exception $e) {
            $this->error_response('Failed to fetch roles', 500);
        }
    }
}

