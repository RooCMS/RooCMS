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
                        'type' => 'timestamp',
                        'null' => false,
                    ],
                    'refresh_expires' => [
                        'type' => 'timestamp',
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'timestamp',
                        'default' => 'CURRENT_TIMESTAMP',
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
                        'type' => 'timestamp',
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
                        'type' => 'timestamp',
                        'default' => 'CURRENT_TIMESTAMP',
                        'null' => false,
                    ],
                    'updated_at' => [
                        'type' => 'timestamp',
                        'null' => false,
                    ],
                    'last_activity' => [
                        'type' => 'timestamp',
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
                    'charset' => 'utf8mb4',
					'collate' => 'utf8mb4_unicode_ci',
                    'auto_increment' => 1,
                ],
            ],
            'TABLE_VERIFICATION_CODES' => [
                'columns' => [
                    'id' => [
                        'type' => 'integer',
                        'length' => 11,
                        'unsigned' => true,
                        'auto_increment' => true,
                        'null' => false,
                    ],
                    'code_hash' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'user_id' => [
                        'type' => 'integer',
                        'length' => 11,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'email' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => true,
                    ],
                    'code_type' => [
                        'type' => 'enum',
                        'values' => ['verification', 'otp', 'unsubscribe', 'password_reset'],
                        'null' => false,
                    ],
                    'expires_at' => [
                        'type' => 'timestamp',
                        'null' => false,
                    ],
                    'used_at' => [
                        'type' => 'timestamp',
                        'null' => true,
                    ],
                    'attempts' => [
                        'type' => 'tinyint',
                        'length' => 3,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'max_attempts' => [
                        'type' => 'tinyint',
                        'length' => 3,
                        'unsigned' => true,
                        'default' => 3,
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'timestamp',
                        'default' => 'CURRENT_TIMESTAMP',
                        'null' => false,
                    ],
                    'updated_at' => [
                        'type' => 'timestamp',
                        'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
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
                        'name' => 'type_idx',
                        'columns' => 'code_type',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'user_id_idx',
                        'columns' => 'user_id',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'email_idx',
                        'columns' => 'email',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'expires_at_idx',
                        'columns' => 'expires_at',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'used_at_idx',
                        'columns' => 'used_at',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'code_hash_idx',
                        'columns' => 'code_hash',
                        'length' => 64,
                    ],
                ],
                'constraints' => [
                    [
                        'type' => 'check',
                        'name' => 'chk_expiration',
                        'expression' => 'expires_at > created_at',
                    ],
                    [
                        'type' => 'check',
                        'name' => 'chk_attempts',
                        'expression' => 'attempts <= max_attempts',
                    ],
                ],
                'options' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                    'auto_increment' => 1,
                ],
            ]
        ]
    ],

    'down' => [
        'drop_tables' => [
            'TABLE_TOKENS',
            'TABLE_USERS',
            'TABLE_VERIFICATION_CODES'
        ]
    ]
]