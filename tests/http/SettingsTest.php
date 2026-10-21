<?php declare(strict_types=1);

/**
 * Public settings endpoint: GET /api/v1/settings
 *
 * The controller returns only the "site" group (see SettingsController::index),
 * so mailer credentials and security keys must never be present in the response.
 */
require_once __DIR__.'/ApiTestCase.php';

class SettingsTest extends ApiTestCase {

    private const FORBIDDEN_KEY_PARTS = ['password', 'passwd', 'secret', 'hash_key', 'token', 'smtp', 'mailer_', 'security_', 'api_key', 'private'];


    public function testPublicSettingsAreAvailableWithoutToken(): void {
        $response = $this->api('GET', '/v1/settings');

        $this->assertApiSuccess($response);
        $this->assertTrue(is_array($this->data($response)), 'data must be an object');
        $this->assertTrue(!empty($response['json']['timestamp']), 'timestamp is missing');
    }


    public function testPublicSettingsHaveSiteShape(): void {
        $data = $this->data($this->api('GET', '/v1/settings'));

        foreach(['site_domain', 'site_name', 'site_description'] as $key) {
            $this->assertTrue(array_key_exists($key, $data), 'public settings must contain '.$key);
            $this->assertTrue(is_string($data[$key]), $key.' must be a string');
        }

        $this->assertTrue($data['site_name'] !== '', 'site_name is required and must not be empty (default is used)');
    }


    public function testPublicSettingsContainOnlySiteGroup(): void {
        $data = $this->data($this->api('GET', '/v1/settings'));

        foreach(array_keys($data) as $key) {
            $this->assertTrue(str_starts_with((string)$key, 'site_'), 'unexpected key in public settings: '.$key);
        }
    }


    public function testPublicSettingsDoNotExposeSecrets(): void {
        $response = $this->api('GET', '/v1/settings');
        $data = $this->data($response);

        foreach(array_keys($data) as $key) {
            foreach(self::FORBIDDEN_KEY_PARTS as $part) {
                $this->assertFalse(str_contains(strtolower((string)$key), $part), 'public settings expose sensitive key '.$key);
            }
        }

        $body = strtolower($response['body']);
        foreach(['security_token_hash_key', 'mailer_password', 'mailer_username', 'mailer_host'] as $needle) {
            $this->assertStringNotContains($needle, $body, 'response must not mention '.$needle);
        }
    }


    public function testPublicSettingsAreReadOnly(): void {
        foreach(['POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
            $response = $this->api($method, '/v1/settings', ['site_name' => 'hacked']);

            $this->assertApiError(404, $response, $method.' /v1/settings:');
        }

        // nothing was changed
        $data = $this->data($this->api('GET', '/v1/settings'));
        $this->assertTrue(($data['site_name'] ?? '') !== 'hacked', 'site_name must not be changed by a public request');
    }
}
