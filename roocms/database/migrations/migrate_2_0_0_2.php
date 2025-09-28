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
 * Migration Two Zero: Install data
 */
return [
	'up' => [
		'data' => [
			'TABLE_SETTINGS' => [
				[
					'category' => 'site',
					'sort_order' => 1,
					'title' => 'Site domain',
					'description' => 'Please specify without http:// or https://',
					'key' => 'site_domain',
					'type' => 'string',
					'options' => null,
					'value' => null,
					'default_value' => '',
					'max_length' => 255,
					'is_required' => 1,
					'is_serialized' => 0
				],
				[
					'category' => 'site',
					'sort_order' => 2,
					'title' => 'Site name',
					'description' => null,
					'key' => 'site_name',
					'type' => 'string',
					'options' => null,
					'value' => null,
					'default_value' => '',
					'max_length' => 255,
					'is_required' => 1,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 1,
					'title' => 'Driver of sending mail',
					'description' => 'Method of sending mail',
					'key' => 'mailer_driver',
					'type' => 'select',
					'options' => '{"mail":"PHP mail()","smtp":"SMTP","sendmail":"sendmail"}',
					'value' => null,
					'default_value' => 'mail',
					'max_length' => 10,
					'is_required' => 1,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 2,
					'title' => 'SMTP host',
					'description' => 'This option is required for SMTP driver',
					'key' => 'mailer_host',
					'type' => 'string',
					'options' => null,
					'value' => null,
					'default_value' => 'localhost',
					'max_length' => 255,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 3,
					'title' => 'SMTP port',
					'description' => 'Usually 587 (TLS) or 465 (SSL)',
					'key' => 'mailer_port',
					'type' => 'integer',
					'options' => null,
					'value' => null,
					'default_value' => '25',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 4,
					'title' => 'SMTP login',
					'description' => 'This option is required for SMTP driver',
					'key' => 'mailer_username',
					'type' => 'string',
					'options' => null,
					'value' => null,
					'default_value' => '',
					'max_length' => 255,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 5,
					'title' => 'SMTP password',
					'description' => 'This option is required for SMTP driver',
					'key' => 'mailer_password',
					'type' => 'string',
					'options' => null,
					'value' => null,
					'default_value' => '',
					'max_length' => 255,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 6,
					'title' => 'Encryption',
					'description' => 'tls or ssl',
					'key' => 'mailer_encryption',
					'type' => 'select',
					'options' => '{"tls":"TLS (STARTTLS)","ssl":"SSL","none":"None"}',
					'value' => null,
					'default_value' => 'tls',
					'max_length' => 10,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 7,
					'title' => 'Email sender',
					'description' => 'Here should be the email of the sender',
					'key' => 'mailer_from',
					'type' => 'email',
					'options' => null,
					'value' => null,
					'default_value' => '',
					'max_length' => 255,
					'is_required' => 1,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 8,
					'title' => 'Sender name',
					'description' => 'Here should be the name of the sender',
					'key' => 'mailer_from_name',
					'type' => 'string',
					'options' => null,
					'value' => null,
					'default_value' => '',
					'max_length' => 255,
					'is_required' => 1,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 9,
					'title' => 'Reply-To email',
					'description' => 'Here should be the email of the sender of the answers',
					'key' => 'mailer_reply_to',
					'type' => 'email',
					'options' => null,
					'value' => null,
					'default_value' => '',
					'max_length' => 255,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 10,
					'title' => 'Reply-To name',
					'description' => 'Here should be the name of the sender of the answers',
					'key' => 'mailer_reply_to_name',
					'type' => 'string',
					'options' => null,
					'value' => null,
					'default_value' => '',
					'max_length' => 255,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 11,
					'title' => 'Max attachment size (bytes)',
					'description' => 'Default 10 MB',
					'key' => 'mailer_max_attachment_size',
					'type' => 'integer',
					'options' => null,
					'value' => null,
					'default_value' => '10485760',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 12,
					'title' => 'Max attachments count',
					'description' => 'Default 10',
					'key' => 'mailer_max_attachments_count',
					'type' => 'integer',
					'options' => null,
					'value' => null,
					'default_value' => '10',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 13,
					'title' => 'Check SSL certificate',
					'description' => 'Check SSL certificate for SMTP driver',
					'key' => 'mailer_verify_peer',
					'type' => 'boolean',
					'options' => null,
					'value' => null,
					'default_value' => '0',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 14,
					'title' => 'Check SSL certificate name',
					'description' => 'Check SSL certificate name for SMTP driver',
					'key' => 'mailer_verify_peer_name',
					'type' => 'boolean',
					'options' => null,
					'value' => null,
					'default_value' => '0',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 15,
					'title' => 'Allow self-signed certificates',
					'description' => 'Allow self signed for SMTP driver',
					'key' => 'mailer_allow_self_signed',
					'type' => 'boolean',
					'options' => null,
					'value' => null,
					'default_value' => '1',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'mailer',
					'sort_order' => 16,
					'title' => 'URI for verification email',
					'description' => 'URI for verification email. Use URI path without domain name. Example: /verify-email',
					'key' => 'mailer_verification_mail_uri',
					'type' => 'string',
					'options' => null,
					'value' => null,
					'default_value' => '/verify-email',
					'max_length' => 255,
					'is_required' => 1,
					'is_serialized' => 0
				],
				[
					'category' => 'security',
					'sort_order' => 1,
					'title' => 'Hash Key',
					'description' => 'Hash Key for token cryptography',
					'key' => 'security_token_hash_key',
					'type' => 'string',
					'options' => null,
					'value' => null,
					'default_value' => 'RooCMS',
					'max_length' => 255,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'security',
					'sort_order' => 2,
					'title' => 'Hash Cost',
					'description' => 'Hash Cost for token cryptography. 10 is good for most servers. If your server is powerful enough, you can set it to more. For calculating optimal cost, you can use CLI script pastcost.php.',
					'key' => 'security_token_hash_cost',
					'type' => 'integer',
					'options' => null,
					'value' => null,
					'default_value' => '10',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'security',
					'sort_order' => 3,
					'title' => 'Token Length',
					'description' => 'Token Length for token cryptography',
					'key' => 'security_token_length',
					'type' => 'integer',
					'options' => null,
					'value' => null,
					'default_value' => '32',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'security',
					'sort_order' => 4,
					'title' => 'Token expires',
					'description' => 'TTL to access token. Default 1 hour.',
					'key' => 'security_token_expires',
					'type' => 'integer',
					'options' => null,
					'value' => null,
					'default_value' => '3600',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'security',
					'sort_order' => 5,
					'title' => 'Refresh token expires',
					'description' => 'TTL to refresh token. Default 24 hours.',
					'key' => 'security_refresh_token_expires',
					'type' => 'integer',
					'options' => null,
					'value' => null,
					'default_value' => '86400',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
				[
					'category' => 'security',
					'sort_order' => 6,
					'title' => 'Minimal password length',
					'description' => 'Minimal password length. Default 8.',
					'key' => 'security_password_length',
					'type' => 'integer',
					'options' => null,
					'value' => null,
					'default_value' => '8',
					'max_length' => null,
					'is_required' => 0,
					'is_serialized' => 0
				],
			],
		],
	],

	'down' => [
		'delete_data' => [
			'TABLE_SETTINGS' => [
				'where_by_driver' => [
					'mysql' => '`key` IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
					'postgresql' => '"key" IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
					'firebird' => '"key" IN (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
				],
				'params' => [
					'site_domain',
					'site_name',
					'mailer_driver',
					'mailer_host',
					'mailer_port',
					'mailer_username',
					'mailer_password',
					'mailer_encryption',
					'mailer_from',
					'mailer_from_name',
					'mailer_reply_to',
					'mailer_reply_to_name',
					'mailer_max_attachment_size',
					'mailer_max_attachments_count',
					'mailer_verify_peer',
					'mailer_verify_peer_name',
					'mailer_allow_self_signed',
				],
			],
		],
	],
];


