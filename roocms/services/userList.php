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



class userListService {

    private readonly User $user;


    /**
     * Constructor
     */
    public function __construct(User $user) {
        $this->user = $user;
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