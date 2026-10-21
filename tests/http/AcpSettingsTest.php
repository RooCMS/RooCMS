<?php declare(strict_types=1);

/**
 * Admin settings API: /api/v1/acp/settings*
 *
 * Executable without credentials: 401 without token, 403 for a regular user.
 * Admin tests need ROOCMS_TEST_ADMIN_LOGIN / _PASSWORD. They change only "site_description" and
 * "mailer_reply_to_name" and restore the original values in cleanup. reset/all and reset/group-* are never
 * called with a successful path; the only reset used is reset/key-site_description (a key changed by the test).
 *
 * Fixtures from migration migrate_2_0_0_2: site_description (string, max 255, optional, default
 * "Open Source Content Management System"), site_name (string, required), site_feedback_email (email, required),
 * mailer_driver (select), mailer_port (integer), mailer_verify_peer (boolean), mailer_reply_to_name (string, optional).
 */
require_once __DIR__.'/ApiTestCase.php';

class AcpSettingsTest extends ApiTestCase {

    private const SAFE_KEY = 'site_description';
    private const SAFE_KEY_DEFAULT = 'Open Source Content Management System';
    private const SECOND_KEY = 'mailer_reply_to_name';
    private const UNKNOWN_KEY = 'no_such_setting_for_tests';


    // ---------------------------------------------------------------- access control (executable without credentials)

    public function testSettingsEndpointsRequireToken(): void {
        foreach($this->guarded_requests() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body);

            $this->assertApiError(401, $response, $method.' '.$path.':');
        }
    }


    public function testSettingsEndpointsAreForbiddenForRegularUser(): void {
        $user = $this->create_user();

        foreach($this->guarded_requests() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, $user['token']);

            $this->assertApiError(403, $response, $method.' '.$path.':');
        }
    }


    public function testSettingsEndpointsAreForbiddenForModerator(): void {
        // written from the code, needs moderator credentials
        $token = $this->token_for('moderator');

        foreach($this->guarded_requests() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, $token);

            $this->assertApiError(403, $response, $method.' '.$path.':');
        }
    }


    /**
     * Requests that must be blocked by the middleware. Write requests target unknown keys and reset targets a
     * protected key, so even a broken middleware cannot damage real data
     *
     * @return array<int, array{0:string, 1:string, 2:?array}>
     */
    private function guarded_requests(): array {
        return [
            ['GET',   '/v1/acp/settings', null],
            ['GET',   '/v1/acp/settings/meta', null],
            ['GET',   '/v1/acp/settings/group-site', null],
            ['GET',   '/v1/acp/settings/key-site_name', null],
            ['PUT',   '/v1/acp/settings/key-'.self::UNKNOWN_KEY, ['value' => 'x']],
            ['PATCH', '/v1/acp/settings', [self::UNKNOWN_KEY => 'x']],
            ['GET',   '/v1/acp/settings/reset/key-security_token_hash_key', null],
        ];
    }


    // ---------------------------------------------------------------- admin: read (written from the code)

    public function testAdminReadsAllSettings(): void {
        $response = $this->api('GET', '/v1/acp/settings', null, $this->token_for('admin'));

        $this->assertApiSuccess($response);
        $data = $this->data($response);

        foreach(['site', 'mailer', 'security', 'gd'] as $group) {
            $this->assertTrue(is_array($data[$group] ?? null), 'group '.$group.' is missing');
        }

        $this->assertTrue(array_key_exists('site_name', $data['site']), 'site.site_name is missing');
        $this->assertTrue(array_key_exists('security_token_hash_key', $data['security']), 'security.security_token_hash_key is missing');
    }


    public function testAdminReadsSettingsWithMeta(): void {
        $response = $this->api('GET', '/v1/acp/settings/meta', null, $this->token_for('admin'));

        $this->assertApiSuccess($response);
        $data = $this->data($response);

        $this->assertTrue(is_array($data['settings'] ?? null), 'settings is missing');
        $this->assertTrue(is_array($data['meta'] ?? null), 'meta is missing');

        $meta = $data['meta'][self::SAFE_KEY] ?? null;
        $this->assertTrue(is_array($meta), 'meta of '.self::SAFE_KEY.' is missing');
        $this->assertSame('site', $meta['category'] ?? null);
        $this->assertSame('string', $meta['type'] ?? null);
        $this->assertTrue(isset($meta['title']), 'meta.title is missing');

        // every setting has a metadata record
        foreach($data['settings'] as $group => $settings) {
            foreach(array_keys($settings) as $key) {
                $this->assertTrue(isset($data['meta'][$key]), 'meta of '.$group.'.'.$key.' is missing');
            }
        }
    }


    public function testAdminReadsGroup(): void {
        $response = $this->api('GET', '/v1/acp/settings/group-site', null, $this->token_for('admin'));

        $this->assertApiSuccess($response);
        $data = $this->data($response);
        $this->assertTrue(array_key_exists('site_name', $data), 'site_name is missing');
        $this->assertTrue(array_key_exists(self::SAFE_KEY, $data), self::SAFE_KEY.' is missing');
    }


    public function testAdminReadsSingleKey(): void {
        $response = $this->api('GET', '/v1/acp/settings/key-site_name', null, $this->token_for('admin'));

        $this->assertApiSuccess($response);
        $data = $this->data($response);
        $this->assertSame('site_name', $data['key'] ?? null);
        $this->assertTrue(is_string($data['value'] ?? null) && $data['value'] !== '', 'value must be a non-empty string');
        $this->assertSame('string', $data['meta']['type'] ?? null);
        $this->assertSame(255, (int)($data['meta']['max_length'] ?? 0));
    }


    public function testAdminGetsNotFoundForUnknownGroupAndKey(): void {
        $token = $this->token_for('admin');

        $this->assertApiError(404, $this->api('GET', '/v1/acp/settings/group-no_such_group', null, $token), 'group:');
        $this->assertApiError(404, $this->api('GET', '/v1/acp/settings/key-'.self::UNKNOWN_KEY, null, $token), 'key:');
        $this->assertApiError(404, $this->api('PUT', '/v1/acp/settings/key-'.self::UNKNOWN_KEY, ['value' => 'x'], $token), 'put key:');
        $this->assertApiError(404, $this->api('GET', '/v1/acp/settings/reset/key-'.self::UNKNOWN_KEY, null, $token), 'reset key:');
        $this->assertApiError(404, $this->api('GET', '/v1/acp/settings/reset/group-no_such_group', null, $token), 'reset group:');
    }


    public function testAdminSettingKeyWithInjectionIsHarmless(): void {
        $token = $this->token_for('admin');
        $key = rawurlencode("x' OR '1'='1");

        $response = $this->api('GET', '/v1/acp/settings/key-'.$key, null, $token);

        $this->assertApiError(404, $response);
    }


    // ---------------------------------------------------------------- admin: update

    public function testAdminUpdatesSettingAndRestoresIt(): void {
        $token = $this->token_for('admin');
        $original = $this->remember_setting(self::SAFE_KEY, $token);

        $new_value = 'apitest description '.bin2hex(random_bytes(4));
        $response = $this->api('PUT', '/v1/acp/settings/key-'.self::SAFE_KEY, ['value' => $new_value], $token);
        $this->assertApiSuccess($response);
        $this->assertSame('Setting updated successfully', $response['json']['message'] ?? null);

        $this->assertSame($new_value, $this->setting_value(self::SAFE_KEY, $token));

        // the public endpoint shows the new value
        $public = $this->api('GET', '/v1/settings');
        $this->assertSame($new_value, $this->data($public, self::SAFE_KEY));

        // the value is also visible in the group listing
        $group = $this->api('GET', '/v1/acp/settings/group-site', null, $token);
        $this->assertSame($new_value, $this->data($group, self::SAFE_KEY));

        $this->assertTrue($original !== $new_value);
    }


    public function testAdminCanClearOptionalSettingToDefault(): void {
        $token = $this->token_for('admin');
        $this->remember_setting(self::SAFE_KEY, $token);

        $this->assertApiSuccess($this->api('PUT', '/v1/acp/settings/key-'.self::SAFE_KEY, ['value' => 'apitest temp '.bin2hex(random_bytes(3))], $token));

        // optional setting: empty string is allowed and the default value is returned for an empty value
        $this->assertApiSuccess($this->api('PUT', '/v1/acp/settings/key-'.self::SAFE_KEY, ['value' => ''], $token));
        $this->assertSame(self::SAFE_KEY_DEFAULT, $this->setting_value(self::SAFE_KEY, $token));
    }


    public function testAdminResetsOnlyTheKeyChangedByTheTest(): void {
        $token = $this->token_for('admin');
        $this->remember_setting(self::SAFE_KEY, $token);

        $this->assertApiSuccess($this->api('PUT', '/v1/acp/settings/key-'.self::SAFE_KEY, ['value' => 'apitest reset '.bin2hex(random_bytes(3))], $token));

        $response = $this->api('GET', '/v1/acp/settings/reset/key-'.self::SAFE_KEY, null, $token);
        $this->assertApiSuccess($response);
        $this->assertSame('Setting reset to default value', $response['json']['message'] ?? null);
        $this->assertSame(self::SAFE_KEY_DEFAULT, $this->setting_value(self::SAFE_KEY, $token));
    }


    public function testAdminUpdateRequiresValueField(): void {
        $token = $this->token_for('admin');
        $before = $this->setting_value(self::SAFE_KEY, $token);

        $this->assertApiError(400, $this->api('PUT', '/v1/acp/settings/key-'.self::SAFE_KEY, ['other' => 'x'], $token), 'no value:');
        $this->assertApiError(400, $this->api('PUT', '/v1/acp/settings/key-'.self::SAFE_KEY, ['value' => null], $token), 'null value:');

        $this->assertSame($before, $this->setting_value(self::SAFE_KEY, $token));
    }


    public function testAdminUpdateStripsHtmlTags(): void {
        $token = $this->token_for('admin');
        $this->remember_setting(self::SAFE_KEY, $token);

        $marker = bin2hex(random_bytes(3));
        $this->assertApiSuccess($this->api('PUT', '/v1/acp/settings/key-'.self::SAFE_KEY, ['value' => '<script>alert(1)</script>apitest '.$marker], $token));

        $stored = (string)$this->setting_value(self::SAFE_KEY, $token);
        $this->assertStringNotContains('<script', $stored);
        $this->assertStringContains('apitest '.$marker, $stored);
    }


    // ---------------------------------------------------------------- admin: validation (nothing is written)

    public function testAdminUpdateRejectsTooLongValue(): void {
        $token = $this->token_for('admin');
        $before = $this->setting_value(self::SAFE_KEY, $token);

        $response = $this->api('PUT', '/v1/acp/settings/key-'.self::SAFE_KEY, ['value' => str_repeat('a', 256)], $token);

        $this->assertApiError(422, $response);
        $this->assertTrue(isset($response['json']['details']['validation_errors'][self::SAFE_KEY]), 'validation_errors.'.self::SAFE_KEY.' is missing');
        $this->assertStringContains('maximum length', $response['json']['details']['validation_errors'][self::SAFE_KEY]);
        $this->assertSame($before, $this->setting_value(self::SAFE_KEY, $token), 'value must not change');
    }


    public function testAdminUpdateRejectsEmptyRequiredValue(): void {
        $token = $this->token_for('admin');
        $before = $this->setting_value('site_name', $token);

        $response = $this->api('PUT', '/v1/acp/settings/key-site_name', ['value' => ''], $token);

        $this->assertApiError(422, $response);
        $this->assertTrue(isset($response['json']['details']['validation_errors']['site_name']), 'validation_errors.site_name is missing');
        $this->assertSame($before, $this->setting_value('site_name', $token), 'required value must not be cleared');
    }


    public function testAdminUpdateRejectsWrongTypes(): void {
        $token = $this->token_for('admin');

        $cases = [
            'mailer_port'          => 'not-a-number',     // integer
            'mailer_verify_peer'   => 'maybe',            // boolean
            'mailer_driver'        => 'carrier-pigeon',   // select
            'site_feedback_email'  => 'not-an-email',     // email
            self::SAFE_KEY         => ['array', 'value'], // string
        ];

        $before = [];
        foreach(array_keys($cases) as $key) {
            $before[$key] = $this->setting_value($key, $token);
        }

        foreach($cases as $key => $value) {
            $response = $this->api('PUT', '/v1/acp/settings/key-'.$key, ['value' => $value], $token);

            $this->assertApiError(422, $response, $key.':');
            $this->assertTrue(isset($response['json']['details']['validation_errors'][$key]), 'validation_errors.'.$key.' is missing');
        }

        foreach($before as $key => $value) {
            $this->assertSame($value, $this->setting_value($key, $token), $key.' must not change');
        }
    }


    // ---------------------------------------------------------------- admin: bulk update

    public function testAdminPatchesMultipleSettingsAndRestoresThem(): void {
        $token = $this->token_for('admin');
        $this->remember_setting(self::SAFE_KEY, $token);
        $this->remember_setting(self::SECOND_KEY, $token);

        $suffix = bin2hex(random_bytes(4));
        $response = $this->api('PATCH', '/v1/acp/settings', [
            self::SAFE_KEY   => 'apitest bulk description '.$suffix,
            self::SECOND_KEY => 'apitest bulk name '.$suffix,
        ], $token);

        $this->assertApiSuccess($response);
        $this->assertSame('Settings updated successfully', $response['json']['message'] ?? null);
        $this->assertSame('apitest bulk description '.$suffix, $this->setting_value(self::SAFE_KEY, $token));
        $this->assertSame('apitest bulk name '.$suffix, $this->setting_value(self::SECOND_KEY, $token));
    }


    public function testAdminPatchIsAllOrNothingOnValidationError(): void {
        $token = $this->token_for('admin');
        $before = $this->setting_value(self::SAFE_KEY, $token);

        $response = $this->api('PATCH', '/v1/acp/settings', [
            self::SAFE_KEY => 'apitest must not be saved '.bin2hex(random_bytes(3)),
            'mailer_port'  => 'not-a-number',
        ], $token);

        $this->assertApiError(422, $response);
        $this->assertTrue(is_array($response['json']['details']['validation_errors'] ?? null), 'validation_errors is missing');
        $this->assertSame($before, $this->setting_value(self::SAFE_KEY, $token), 'valid part of an invalid patch must not be saved');
    }


    public function testAdminPatchRejectsUnknownKeyAndEmptyBody(): void {
        $token = $this->token_for('admin');

        $unknown = $this->api('PATCH', '/v1/acp/settings', [self::UNKNOWN_KEY => 'x'], $token);
        $this->assertApiError(422, $unknown, 'unknown key:');

        $this->assertApiError(400, $this->api('PATCH', '/v1/acp/settings', [], $token), 'empty body:');
        $this->assertApiError(400, $this->http('PATCH', '/api/v1/acp/settings', '{broken', ['Content-Type' => 'application/json', 'Authorization' => 'Bearer '.$token]), 'broken json:');
    }


    // ---------------------------------------------------------------- admin: protected key

    /**
     * Reset of the token signing key is refused; the key value stays the same
     */
    public function testResetOfSecurityTokenHashKeyIsRefused(): void {
        $token = $this->token_for('admin');
        $before = $this->setting_value('security_token_hash_key', $token);

        $response = $this->api('GET', '/v1/acp/settings/reset/key-security_token_hash_key', null, $token);

        $this->assertTrue($response['status'] >= 400, 'reset of security_token_hash_key must be refused, got '.$response['status']);
        $this->assertTrue(($response['json']['error'] ?? false) === true, 'refusal must be an error response');
        $this->assertSame($before, $this->setting_value('security_token_hash_key', $token), 'security_token_hash_key must not change');
    }


    // ---------------------------------------------------------------- helpers

    /**
     * Current effective value of a setting through the admin API
     */
    private function setting_value(string $key, string $token): mixed {
        $response = $this->api('GET', '/v1/acp/settings/key-'.$key, null, $token);
        $this->assertApiSuccess($response, 200, 'read '.$key.':');

        return $this->data($response, 'value');
    }


    /**
     * Read the current value and register a cleanup that writes it back
     */
    private function remember_setting(string $key, string $token): mixed {
        $original = $this->setting_value($key, $token);

        $this->on_cleanup(function() use ($key, $token, $original) {
            $this->api('PUT', '/v1/acp/settings/key-'.$key, ['value' => $original ?? ''], $token);
        });

        return $original;
    }
}
