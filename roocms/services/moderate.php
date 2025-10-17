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
 * ModerateService
 * Service for moderating content
 */
class ModerateService {

    private User $user;



    /**
     * Constructor
     */
    public function __construct(User $user) {
        $this->user = $user;
    }


    /**
     * Ban user with optional timestamp and reason
     * 
     * @param int $user_id User ID
     * @param int $until Unix Timestamp until which the user is banned (0 = permanent ban)
     * @param string $reason Reason for banning (optional)
     * @return bool True on success, false on failure
     */
    public function ban(int $user_id, int $until = 0, string $reason = ''): bool {
        return $this->user->update_user($user_id, [
            'is_banned' => 1,
            'ban_expired' => $until,
            'ban_reason' => $reason
        ]);
    }


    /**
     * Unban user
     * 
     * @param int $user_id User ID
     * @return bool True on success, false on failure
     */
    public function unban(int $user_id): bool {
        return $this->user->update_user($user_id, [
            'is_banned' => 0,
            'ban_expired' => 0,
            'ban_reason' => ''
        ]);
    }
}