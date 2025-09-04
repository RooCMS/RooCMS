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
 * Migration One: Install tokens table
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
						'columns' => ['hash', 'user_id', 'token_expires'],
					],
					[
						'type' => 'key',
						'name' => 'tokens_refresh_idx',
						'columns' => ['refresh', 'refresh_expires', 'created_at'],
					],
					[
						'type' => 'key',
						'name' => 'tokens_created_at_idx',
						'columns' => 'created_at',
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
            ]
        ]
    ],

    'down' => [
        'drop_tables' => [
            'TABLE_TOKENS'
        ]
    ]
]