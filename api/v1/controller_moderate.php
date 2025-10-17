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
 * Moderate Controller
 * Controller for moderating content
 */
class ModerateController extends BaseController {

    private readonly ModerateService $moderateService;
    private readonly User $user;



    /**
     * Constructor
     */
    public function __construct(Db $db, Request $request, ModerateService $moderateService, User $user) {
        parent::__construct($db, $request);

        $this->moderateService = $moderateService;
        $this->user = $user;
    }


    /**
     * Ban user
     * POST /api/v1/moderate/ban/{user_id}
     * Requires: AuthMiddleware + RoleMiddleware@moderator_access
     * 
     * @param int $user_id User ID to ban
     * @return void
     */
    public function ban(int $user_id): void {
        $this->log_request('moderate_ban', ['user_id' => $user_id]);

        // Check if user exists
        if (!$this->user->get_user_by_id($user_id)) {
            $this->error_response('User not found', 404);
            return;
        }

        $data = $this->get_input_data();
        $until = $this->validate_until($data['until'] ?? null);
        $reason = $this->validate_reason($data['reason'] ?? '');

        if ($until === false || $reason === false) {
            return; // Error already sent
        }

        try {
            if ($this->moderateService->ban($user_id, $until, $reason)) {
                $this->json_response(null, 200, 'User banned successfully');
            } else {
                $this->error_response('Failed to ban user', 500);
            }
        } catch(Exception $e) {
            $this->error_response('Failed to ban user', 500);
        }
    }


    /**
     * Unban user
     * GET /api/v1/moderate/unban/{user_id}
     * Requires: AuthMiddleware + RoleMiddleware@moderator_access
     * 
     * @param int $user_id User ID to unban
     * @return void
     */
    public function unban(int $user_id): void {
        $this->log_request('moderate_unban', ['user_id' => $user_id]);

        // Check if user exists
        if (!$this->user->get_user_by_id($user_id)) {
            $this->error_response('User not found', 404);
            return;
        }

        try {
            if ($this->moderateService->unban($user_id)) {
                $this->json_response(null, 200, 'User unbanned successfully');
            } else {
                $this->error_response('Failed to unban user', 500);
            }
        } catch(Exception $e) {
            $this->error_response('Failed to unban user', 500);
        }
    }


    /**
     * Validate until timestamp
     * 
     * @param mixed $until
     * @return int|false Returns timestamp or false on error
     */
    private function validate_until(mixed $until): int|false {
        if ($until === null) {
            return 0; // Permanent ban
        }

        if (!is_int($until) && !is_numeric($until)) {
            $this->error_response('Invalid until timestamp format', 400);
            return false;
        }

        $until = (int)$until;

        // Check if timestamp is not in the past (except 0 for permanent ban)
        if ($until > 0 && $until < time()) {
            $this->error_response('Ban until timestamp cannot be in the past', 400);
            return false;
        }

        return $until;
    }


    /**
     * Validate ban reason
     * 
     * @param mixed $reason
     * @return string|false Returns reason string or false on error
     */
    private function validate_reason(mixed $reason): string|false {
        if (!is_string($reason)) {
            $this->error_response('Invalid reason format', 400);
            return false;
        }

        return trim($reason);
    }
}