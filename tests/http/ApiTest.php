<?php declare(strict_types=1);

/**
 * HTTP tests against a running RooCMS instance
 *
 * See ApiTestCase for environment variables
 *
 * The tests are read-only and use no credentials: they never create accounts or change data.
 */
require_once __DIR__.'/ApiTestCase.php';

class ApiTest extends ApiTestCase {


    public function testHealthEndpointIsPublic(): void {
        $response = $this->http('GET', '/api/v1/health');

        $this->assertSame(200, $response['status']);
        $this->assertTrue($response['json'] !== null, 'Health must return JSON');
    }


    public function testUnknownEndpointReturnsJson404(): void {
        $response = $this->http('GET', '/api/v1/this-route-does-not-exist');

        $this->assertSame(404, $response['status']);
        $this->assertTrue(($response['json']['error'] ?? false) === true);
    }


    public function testUncaughtExceptionReturnsJson500(): void {
        // A PHP filename is rejected by sanitize_path before API dispatch.
        $response = $this->http('GET', '/api/v1/controller_health.php');

        $this->assertSame(500, $response['status']);
        $this->assertStringContains('application/json', $response['headers']['content-type'] ?? '');
        $this->assertStringContains('charset=utf-8', $response['headers']['content-type'] ?? '');
        $this->assertTrue($response['json'] !== null, 'Uncaught exceptions must return valid JSON');
        $this->assertTrue(in_array($response['json']['error'] ?? null, ['internal_error', 'exception'], true));
    }


    public function testPreflightRequestIsAnswered(): void {
        $response = $this->http('OPTIONS', '/api/v1/users');

        $this->assertSame(200, $response['status']);
        $this->assertStringContains('Authorization', $response['headers']['access-control-allow-headers'] ?? '');
    }


    public function testProtectedEndpointsRequireToken(): void {
        $endpoints = [
            ['GET',  '/api/v1/users'],
            ['GET',  '/api/v1/users/me'],
            ['GET',  '/api/v1/acp/debug/logs'],
            ['POST', '/api/v1/acp/debug/clear'],
            ['GET',  '/api/v1/acp/settings'],
            ['GET',  '/api/v1/acp/settings/meta'],
            ['GET',  '/api/v1/acp/structure'],
            ['GET',  '/api/v1/backup/list'],
            ['POST', '/api/v1/moderate/ban/1'],
            ['GET',  '/api/v1/moderate/unban/1'],
        ];

        foreach($endpoints as [$method, $path]) {
            $response = $this->http($method, $path);
            $this->assertSame(401, $response['status'], $method.' '.$path.' must require a token');
        }
    }


    public function testInvalidTokenIsRejected(): void {
        $response = $this->http('GET', '/api/v1/users/me', null, ['Authorization' => 'Bearer not-a-real-token']);

        $this->assertSame(401, $response['status']);
    }


    public function testLoginWithUnknownCredentialsFails(): void {
        $response = $this->http(
            'POST',
            '/api/v1/auth/login',
            json_encode(['login' => 'no_such_user_for_tests', 'password' => 'wrong-password-123']),
            ['Content-Type' => 'application/json']
        );

        $this->assertSame(401, $response['status']);
    }


    public function testRegistrationValidatesRequiredFields(): void {
        // empty body: nothing is created, validation fails before the service is called
        $response = $this->http('POST', '/api/v1/auth/register', '{}', ['Content-Type' => 'application/json']);

        $this->assertSame(422, $response['status']);
    }


    /**
     * Broken JSON must be a client error, not a server error
     * (see SanitizeTest::testSafeJsonDecodeReturnsNullOnBrokenJson)
     */
    public function testBrokenJsonBodyIsNotAServerError(): void {
        $response = $this->http('POST', '/api/v1/auth/login', '{broken', ['Content-Type' => 'application/json']);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'Got status '.$response['status']);
    }


    public function testDebugPageDoesNotEmbedLogs(): void {
        // the page is a static shell: logs are loaded by JS from the admin-only API
        $response = $this->http('GET', '/!/acp/debug');

        $this->assertSame(200, $response['status']);
        $this->assertStringContains('x-data="debugLogsManager"', $response['body']);
        $this->assertStringNotContains('&quot;timestamp&quot;', $response['body']);
        $this->assertStringNotContains('&quot;query_stats&quot;', $response['body']);
    }


    public function testContentSecurityPolicyBlocksInlineScripts(): void {
        $response = $this->http('GET', '/');

        $csp = $response['headers']['content-security-policy'] ?? '';
        $this->assertTrue($csp !== '', 'CSP header is missing');

        preg_match("/script-src ([^;]+)/", $csp, $m);
        $script_src = $m[1] ?? '';

        $this->assertStringContains("'nonce-", $script_src);
        $this->assertStringNotContains("'unsafe-inline'", $script_src);
    }
}
