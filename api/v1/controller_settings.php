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
 * Settings Controller
 * API for managing site settings
 */
class SettingsController extends BaseController {

    private readonly SiteSettingsService $SiteSettingsService;


    /**
     * Constructor
     */
    public function __construct(SiteSettingsService $SiteSettingsService, Db $db, Request $request) {
        parent::__construct($db, $request);
        $this->SiteSettingsService = $SiteSettingsService;
    }


    /**
     * Get all settings
     * GET /api/v1/settings
     */
    public function index(): void {
        $this->log_request('settings_index');

        $settings = $this->SiteSettingsService->get_settings_by_group('site');
        $this->json_response($settings);
    }
}