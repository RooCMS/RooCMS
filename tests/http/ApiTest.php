<?php declare(strict_types=1);

/**
 * HTTP tests against a running RooCMS instance
 *
 * Base URL:  ROOCMS_TEST_URL (default https://dev.roocms.com)
 * Self-signed certificate: set ROOCMS_TEST_INSECURE=1
 *
 * The tests are read-only and use no credentials: they never create accounts or change data.
 */
class ApiTest extends TestCase {

    private string $base_url;


    protected function setUp(): void {
        $this->base_url = rtrim((string)(getenv('ROOCMS_TEST_URL') ?: 'https://dev.roocms.com'), '/');
    }


    /**
     * Send request and return status, headers, body and decoded JSON
     * Skips the test if the server is unreachable
     */
    private function http(string $method, string $path, ?string $body = null, array $headers = []): array {
        $header_lines = [];
        foreach($headers as $name => $value) {
            $header_lines[] = $name.': '.$value;
        }

        $context = stream_context_create([
            'http' => [
                'method'        => $method,
                'header'        => implode("\r\n", $header_lines),
                'content'       => $body ?? '',
                'ignore_errors' => true,
                'timeout'       => 15,
                'follow_location' => 0,
            ],
            'ssl' => [
                'verify_peer'      => getenv('ROOCMS_TEST_INSECURE') !== '1',
                'verify_peer_name' => getenv('ROOCMS_TEST_INSECURE') !== '1',
            ],
        ]);

        $content = @file_get_contents($this->base_url.$path, false, $context);
        if($content === false || empty($http_response_header)) {
            $this->skip('server is unreachable: '.$this->base_url);
        }

        preg_match('#^HTTP/\S+\s+(\d{3})#', $http_response_header[0], $m);

        $parsed_headers = [];
        foreach(array_slice($http_response_header, 1) as $line) {
            if(str_contains($line, ':')) {
                [$name, $value] = explode(':', $line, 2);
                $parsed_headers[strtolower(trim($name))] = trim($value);
            }
        }

        $json = json_decode($content, true);

        return [
            'status'  => (int)($m[1] ?? 0),
            'headers' => $parsed_headers,
            'body'    => $content,
            'json'    => is_array($json) ? $json : null,
        ];
    }


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
