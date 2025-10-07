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
                    'token' => [
                        'type' => 'string',
                        'length' => 64,
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
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                    ],
                    'refresh_expires' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
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
						'name' => 'idx_token',
						'columns' => ['token', 'token_expires'],
					],
					[
						'type' => 'key',
						'name' => 'idx_refresh',
						'columns' => ['refresh', 'refresh_expires'],
					],
                    [
                        'type' => 'unique',
                        'name' => 'uq_tokens_token',
                        'columns' => 'token',
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'uq_tokens_refresh',
                        'columns' => 'refresh',
                    ],
					[
						'type' => 'key',
						'name' => 'idx_user_id',
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
                    'id' => [
                        'type' => 'integer',
                        'length' => 11,
                        'unsigned' => true,
                        'auto_increment' => true,
                        'null' => false,
                    ],
                    'role' => [
						'type' => 'enum',
						'values' => ['u', 'm', 'a', 'su'],
						'default' => 'u',
						'null' => false
					],
                    'is_active' => [
                        'type' => 'boolean',
                        'default' => 0,
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
                        'type' => 'boolean',
                        'default' => 0,
                        'null' => false,
                    ],
                    'is_banned' => [
                        'type' => 'boolean',
                        'default' => 0,
                        'null' => false,
                    ],
                    'ban_expired' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                    'ban_reason' => [
                        'type' => 'string',
                        'length' => 512,
                        'default' => '',
                        'null' => false,
                    ],
                    'password' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                    'updated_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                    'last_activity' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                    'is_deleted' => [
                        'type' => 'boolean',
                        'default' => 0,
                        'null' => false,
                    ],
                    'deleted_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => true,
                        'default' => 0,
                    ],
                ],
                'indexes' => [
                    [
                        'type' => 'primary',
                        'columns' => 'id',
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'uq_users_login',
                        'columns' => 'login',
                    ],
                    [
						'type' => 'unique',
						'name' => 'uq_users_email',
						'columns' => 'email',
					],
                    [
						'type' => 'key',
						'name' => 'idx_role',
						'columns' => 'role'
					],
					[
						'type' => 'key',
						'name' => 'idx_is_deleted',
						'columns' => 'is_deleted',
					],
                ],
                'options' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
					'collate' => 'utf8mb4_unicode_ci',
                    'auto_increment' => 1,
                ],
            ],
            'TABLE_USER_PROFILES' => [
				'columns' => [
					'user_id' => [
						'type' => 'integer',
						'length' => 11,
						'unsigned' => true,
						'null' => false,
					],
					'nickname' => [
						'type' => 'string',
						'length' => 64,
						'null' => true,
					],
					'first_name' => [
						'type' => 'string',
						'length' => 64,
						'null' => true,
					],
					'last_name' => [
						'type' => 'string',
						'length' => 64,
						'null' => true,
					],
                    'gender' => [
						'type' => 'enum',
						'values' => ['male', 'female', 'other'],
						'null' => true,
					],
					'avatar' => [
						'type' => 'string',
						'length' => 255,
						'null' => true,
					],
					'bio' => [
						'type' => 'text',
						'null' => true,
					],
					'birthday' => [
						'type' => 'date',
						'null' => true,
					],
					'website' => [
						'type' => 'string',
						'length' => 200,
						'null' => true,
					],
                    'is_public' => [
						'type' => 'boolean',
						'default' => 1,
						'null' => false,
					],
					'created_at' => [
						'type' => 'bigint',
						'length' => 20,
						'unsigned' => true,
						'null' => false,
						'default' => 0,
					],
					'updated_at' => [
						'type' => 'bigint',
						'length' => 20,
						'unsigned' => true,
						'null' => false,
						'default' => 0,
					]
				],
				'indexes' => [
					[
						'type' => 'primary',
						'columns' => 'user_id',
					],
					[
						'type' => 'unique',
						'name' => 'uq_user_profiles_nickname',
						'columns' => 'nickname',
					]
				],
				'options' => [
					'engine' => 'InnoDB',
					'charset' => 'utf8mb4',
					'collate' => 'utf8mb4_unicode_ci',
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
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                    'used_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => true,
                        'default' => 0,
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
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                    'updated_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                ],
                'indexes' => [
                    [
                        'type' => 'primary',
                        'columns' => 'id',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_type',
                        'columns' => 'code_type',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_user_id',
                        'columns' => 'user_id',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_email',
                        'columns' => 'email',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_expires_at',
                        'columns' => 'expires_at',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_used_at',
                        'columns' => 'used_at',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_code_hash',
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
            ],
            'TABLE_SETTINGS' => [
                'columns' => [
                    'id' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'auto_increment' => true,
                        'null' => false,
                    ],
                    'category' => [
                        'type' => 'string',
                        'length' => 255,
                        'default' => 'general',
                        'null' => false,
                    ],
                    'sort_order' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'default' => 1,
                        'null' => false,
                    ],
                    'title' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'description' => [
                        'type' => 'text',
                        'null' => true,
                    ],
                    'key' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'type' => [
                        'type' => 'enum',
                        'values' => ['boolean', 'integer', 'string', 'color', 'text', 'html', 'date', 'email', 'select', 'image', 'file'],
                        'default' => 'string',
                        'null' => false,
                    ],
                    'options' => [
                        'type' => 'json',
                        'null' => true,
                    ],
                    'value' => [
                        'type' => 'longtext',
                        'null' => true,
                    ],
                    'default_value' => [
                        'type' => 'longtext',
                        'null' => true,
                    ],
                    'max_length' => [
                        'type' => 'smallint',
                        'length' => 5,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'is_required' => [
                        'type' => 'tinyint',
                        'length' => 1,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'is_serialized' => [
                        'type' => 'tinyint',
                        'length' => 1,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                    'updated_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                ],
                'indexes' => [
                    [
                        'type' => 'primary',
                        'columns' => 'id',
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'uq_setting_key',
                        'columns' => 'key',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_setting_category',
                        'columns' => 'category',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_setting_sort',
                        'columns' => ['category', 'sort_order'],
                    ],
                ],
                'options' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                ],
            ],
            'TABLE_MEDIA' => [
                'columns' => [
                    'id' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'auto_increment' => true,
                        'null' => false,
                    ],
                    'uuid' => [
                        'type' => 'char',
                        'length' => 36,
                        'null' => false,
                    ],
                    'user_id' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'original_name' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'filename' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'file_path' => [
                        'type' => 'string',
                        'length' => 500,
                        'null' => false,
                    ],
                    'mime_type' => [
                        'type' => 'string',
                        'length' => 127,
                        'null' => false,
                    ],
                    'file_size' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'null' => false,
                    ],
                    'media_type' => [
                        'type' => 'enum',
                        'values' => ['image', 'document', 'video', 'audio', 'archive', 'other'],
                        'null' => false,
                    ],
                    'extension' => [
                        'type' => 'string',
                        'length' => 20,
                        'null' => false,
                    ],
                    'width' => [
                        'type' => 'smallint',
                        'length' => 5,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'height' => [
                        'type' => 'smallint',
                        'length' => 5,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'duration' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'metadata' => [
                        'type' => 'json',
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_bin',
                        'null' => true,
                    ],
                    'status' => [
                        'type' => 'enum',
                        'values' => ['uploaded', 'processing', 'ready', 'error', 'deleted'],
                        'default' => 'uploaded',
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                    'updated_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                ],
                'indexes' => [
                    [
                        'type' => 'primary',
                        'columns' => 'id',
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'uuid',
                        'columns' => 'uuid',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_media_type',
                        'columns' => 'media_type',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_user_id',
                        'columns' => 'user_id',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_status',
                        'columns' => 'status',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_created_at',
                        'columns' => 'created_at',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_mime_type',
                        'columns' => 'mime_type',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_uuid',
                        'columns' => 'uuid',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_original_name',
                        'columns' => 'original_name',
                        'length' => 255,
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_file_size',
                        'columns' => 'file_size',
                    ],
                ],
                'options' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                ],
            ],
            'TABLE_MEDIA_VARS' => [
                'columns' => [
                    'id' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'auto_increment' => true,
                        'null' => false,
                    ],
                    'media_id' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                    ],
                    'variant_type' => [
                        'type' => 'string',
                        'length' => 64,
                        'null' => false,
                    ],
                    'file_path' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'file_size' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'null' => false,
                    ],
                    'width' => [
                        'type' => 'smallint',
                        'length' => 5,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'height' => [
                        'type' => 'smallint',
                        'length' => 5,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'quality' => [
                        'type' => 'tinyint',
                        'length' => 3,
                        'unsigned' => true,
                        'null' => true,
                    ],
                    'mime_type' => [
                        'type' => 'string',
                        'length' => 127,
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                ],
                'indexes' => [
                    [
                        'type' => 'primary',
                        'columns' => 'id',
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'unique_variant',
                        'columns' => ['media_id', 'variant_type'],
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_variant_type',
                        'columns' => 'variant_type',
                    ],
                ],
                'options' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                ],
            ],
            'TABLE_MEDIA_RELS' => [
                'columns' => [
                    'id' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'auto_increment' => true,
                        'null' => false,
                    ],
                    'media_id' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                    ],
                    'entity_type' => [
                        'type' => 'string',
                        'length' => 64,
                        'null' => false,
                    ],
                    'entity_id' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                    ],
                    'relationship_type' => [
                        'type' => 'string',
                        'length' => 64,
                        'null' => false,
                    ],
                    'sort_order' => [
                        'type' => 'smallint',
                        'length' => 5,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'metadata' => [
                        'type' => 'longtext',
                        'charset' => 'utf8mb4',
                        'collate' => 'utf8mb4_bin',
                        'null' => true,
                    ],
                    'created_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0,
                    ],
                ],
                'indexes' => [
                    [
                        'type' => 'primary',
                        'columns' => 'id',
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'unique_relationship',
                        'columns' => ['media_id', 'entity_type', 'entity_id', 'relationship_type'],
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_entity',
                        'columns' => ['entity_type', 'entity_id'],
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_relationship_type',
                        'columns' => 'relationship_type',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_sort_order',
                        'columns' => 'sort_order',
                    ],
                ],
                'constraints' => [
                    [
                        'type' => 'check',
                        'name' => 'chk_metadata_json',
                        'expression' => 'json_valid(metadata)',
                    ],
                ],
                'options' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                ],
            ],
            'TABLE_STRUCTURE' => [
                'columns' => [
                    'id' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'auto_increment' => true,
                        'null' => false,
                    ],
                    'status' => [
                        'type' => 'enum',
                        'values' => ['draft', 'active', 'inactive'],
                        'default' => 'draft',
                        'null' => false,
                    ],
                    'slug' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => false,
                    ],
                    'parent_id' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'default' => 1,
                        'null' => false,
                    ],
                    'nav' => [
                        'type' => 'enum',
                        'values' => ['0', '1'],
                        'default' => '1',
                        'null' => false,
                    ],
                    'title' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => true,
                    ],
                    'meta_title' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => true,
                    ],
                    'meta_description' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => true,
                    ],
                    'meta_keywords' => [
                        'type' => 'string',
                        'length' => 255,
                        'null' => true,
                    ],
                    'sort' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'page_type' => [
                        'type' => 'enum',
                        'values' => ['page', 'feed'],
                        'default' => 'page',
                        'null' => false,
                    ],
                    'noindex' => [
                        'type' => 'enum',
                        'values' => ['0', '1'],
                        'default' => '0',
                        'null' => false,
                    ],
                    'childs' => [
                        'type' => 'integer',
                        'length' => 10,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'created_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'updated_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                    'published_at' => [
                        'type' => 'bigint',
                        'length' => 20,
                        'unsigned' => true,
                        'default' => 0,
                        'null' => false,
                    ],
                ],
                'indexes' => [
                    [
                        'type' => 'primary',
                        'columns' => 'id',
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'uq_structure_id',
                        'columns' => 'id'
                    ],
                    [
                        'type' => 'unique',
                        'name' => 'uq_structure_slug',
                        'columns' => 'slug',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_structure_parent_id',
                        'columns' => 'parent_id',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_structure_sort',
                        'columns' => 'sort',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_structure_type',
                        'columns' => 'page_type',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_structure_status',
                        'columns' => 'status',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_structure_nav',
                        'columns' => 'nav',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_structure_published_at',
                        'columns' => 'published_at',
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_structure_status_nav',
                        'columns' => ['status', 'nav'],
                    ],
                    [
                        'type' => 'key',
                        'name' => 'idx_structure_type_status',
                        'columns' => ['page_type', 'status'],
                    ],
                ],
                'options' => [
                    'engine' => 'InnoDB',
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                ],
            ],
        ],
        'add_foreign_keys' => [
            'TABLE_USER_PROFILES' => [
                [
                    'name' => 'fk_user_profiles_user_id',
                    'columns' => ['user_id'],
                    'reference_table' => 'TABLE_USERS',
                    'reference_columns' => ['id'],
                    'on_delete' => 'CASCADE',
                ],
            ],
            'TABLE_VERIFICATION_CODES' => [
                [
                    'name' => 'fk_verification_codes_user_id',
                    'columns' => ['user_id'],
                    'reference_table' => 'TABLE_USERS',
                    'reference_columns' => ['id'],
                    'on_delete' => 'CASCADE',
                ],
            ],
            'TABLE_TOKENS' => [
                [
                    'name' => 'fk_token_user_id',
                    'columns' => ['user_id'],
                    'reference_table' => 'TABLE_USERS',
                    'reference_columns' => ['id'],
                    'on_delete' => 'CASCADE',
                ],
            ],
            'TABLE_MEDIA_VARS' => [
                [
                    'name' => 'fk_media_vars',
                    'columns' => ['media_id'],
                    'reference_table' => 'TABLE_MEDIA',
                    'reference_columns' => ['id'],
                    'on_delete' => 'CASCADE',
                ],
            ],
            'TABLE_MEDIA_RELS' => [
                [
                    'name' => 'fk_media_rels',
                    'columns' => ['media_id'],
                    'reference_table' => 'TABLE_MEDIA',
                    'reference_columns' => ['id'],
                    'on_delete' => 'CASCADE',
                ],
            ],
        ],
    ],

    'down' => [
        'drop_tables' => [
            'TABLE_TOKENS',
            'TABLE_USER_PROFILES',
            'TABLE_VERIFICATION_CODES',
            'TABLE_USERS',
            'TABLE_SETTINGS',
            'TABLE_MEDIA',
            'TABLE_MEDIA_VARS',
            'TABLE_MEDIA_RELS',
            'TABLE_STRUCTURE',
        ]
    ]
];