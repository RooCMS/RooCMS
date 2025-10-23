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
 * Debug Controller
 * API for managing debug logs
 */
class DebugController extends BaseController {

    private DebugService $debugService;

    /**
     * Constructor
     */
    public function __construct(Db $db, Request $request, DebugService $debugService) {
        parent::__construct($db, $request);
        $this->debugService = $debugService;
    }


    /**
     * Clear debug logs
     * POST /api/v1/admin/debug/clear
     * Requires: AuthMiddleware + RoleMiddleware@admin_access
     */
    public function clear(): void {
        $this->log_request('debug_clear');

        try {
            $this->debugService->clear_debug_logs();

            $this->json_response([
                'status' => 'success',
                'message' => 'Debug logs cleared successfully'
            ]);

        } catch (Exception $e) {
            $this->json_response([
                'status' => 'error',
                'message' => 'Failed to clear debug logs: ' . $e->getMessage()
            ], 500);
        }
    }
}
