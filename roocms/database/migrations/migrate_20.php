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
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################




/**
 * Migration Two Zero: Install tables
 */
return [
    'up' => [
        'tables' => [
            'TABLE_TOKENS' => [
                'columns' => [
                    'id' => [
                        'type' => 'integer',
                        'length' => 11,
                        'auto_increment' => true,
                        'null' => false,
                    ],
                    'hash' => [
                        'type' => 'string',
                        'length' => 32,
                        'null' => false,
                    ],
                    'refresh' => [
                        'type' => 'string',
                        'length' => 64,
                        'null' => false,
                    ],
                    'user_id' => [
                        'type' => 'integer',
                        'length' => 11,
                        'null' => false,
                    ],
                    'token_expires' => [
                        'type' => 'integer',
                        'length' => 10,
                        'null' => false,
                    ],
                    'refresh_expires' => [
                        'type' => 'integer',
                        'length' => 10,
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'integer',
                        'length' => 10,
                        'null' => false,
                    ],
                ],
                'indexes' => [
					[
						'type' => 'primary',
						'columns' => 'id',
					],
					[
						'type' => 'key',
						'name' => 'tokens_hash_idx',
						'columns' => ['hash', 'token_expires'],
					],
					[
						'type' => 'key',
						'name' => 'tokens_refresh_idx',
						'columns' => ['refresh', 'refresh_expires'],
					],
                    [
                        'type' => 'unique',
                        'name' => 'tokens_uhash_idx',
                        'columns' => 'hash',
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'tokens_urefresh_idx',
                        'columns' => 'refresh',
                    ],
					[
						'type' => 'key',
						'name' => 'tokens_user_id_idx',
						'columns' => 'user_id',
					]
                ],
                'options' => [
					'engine' => 'InnoDB',
					'charset' => 'utf8mb4',
					'collate' => 'utf8mb4_unicode_ci',
					'auto_increment' => 1,
				],
            ],
            'TABLE_USERS' => [
                'columns' => [
                    'user_id' => [
                        'type' => 'integer',
                        'length' => 11,
                        'unsigned' => true,
                        'auto_increment' => true,
                        'null' => false,
                    ],
                    'is_active' => [
                        'type' => 'enum',
                        'values' => ['0', '1'],
                        'default' => '0',
                        'null' => false,
                    ],
                    'login' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'email' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'is_verified' => [
                        'type' => 'enum',
                        'values' => ['0', '1'],
                        'default' => '0',
                        'null' => false,
                    ],
                    'is_banned' => [
                        'type' => 'enum',
                        'values' => ['0', '1'],
                        'default' => '0',
                        'null' => false,
                    ],
                    'ban_expired' => [
                        'type' => 'integer',
                        'length' => 11,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'ban_reason' => [
                        'type' => 'string',
                        'length' => 512,
                        'default' => '',
                        'null' => false,
                    ],
                    'password' => [
                        'type' => 'string',
                        'length' => 64,
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'integer',
                        'length' => 11,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'updated_at' => [
                        'type' => 'integer',
                        'length' => 11,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'last_activity' => [
                        'type' => 'integer',
                        'length' => 11,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                ],
                'indexes' => [
                    [
                        'type' => 'primary',
                        'columns' => 'user_id',
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'login',
                        'columns' => 'login',
                    ],
                ],
                'options' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4'
					'collate' => 'utf8mb4_unicode_ci',
                    'auto_increment' => 1,
                ],
            ]
        ]
    ],

    'down' => [
        'drop_tables' => [
            'TABLE_TOKENS',
            'TABLE_USERS'
        ]
    ]
]