<?php declare(strict_types=1);

/**
 * System endpoints: API root listing, health, CSP report, debug logs
 *
 * Executable without credentials: root, health, csp-report, 401/403 checks.
 * Needs ROOCMS_TEST_ADMIN_*: admin read of debug logs. The debug "clear" endpoint is never called as admin
 * because it destroys logs.
 */
require_once __DIR__.'/ApiTestCase.php';

class SystemTest extends ApiTestCase {

    private const CSP_REPORT = [
        'csp-report' => [
            'document-uri'       => 'https://dev.roocms.com/',
            'violated-directive' => 'script-src',
            'blocked-uri'        => 'inline',
        ],
    ];


    // ---------------------------------------------------------------- root listing

    public function testRootListsApiInformation(): void {
        $response = $this->http('GET', '/api/');

        $this->assertApiSuccess($response);
        $this->assertSame('RooCMS API', $response['json']['message'] ?? null);
        $this->assertTrue(is_string($response['json']['version'] ?? null) && $response['json']['version'] !== '', 'version is missing');
        $this->assertTrue(is_array($response['json']['endpoints'] ?? null) && $response['json']['endpoints'] !== [], 'endpoints must be a non-empty object');
    }


    public function testRootListingContainsEveryRegisteredRoute(): void {
        $router = file_get_contents(__DIR__.'/../../api/router.php');
        $this->assertTrue($router !== false, 'cannot read api/router.php');

        preg_match_all('#^\s*\$api->(get|post|put|patch|delete)\(\'(/v1/[^\']*)\'#m', $router, $m, PREG_SET_ORDER);
        $this->assertTrue(count($m) > 20, 'routes were not parsed from router.php');

        $listing = $this->http('GET', '/api/')['json']['endpoints'] ?? [];
        $listed = array_values($listing);

        $missing = [];
        foreach($m as $route) {
            $expected = strtoupper($route[1]).' /api'.$route[2];
            if(!in_array($expected, $listed, true)) {
                $missing[] = $expected;
            }
        }

        $this->assertSame([], $missing, 'routes registered in router.php but missing in GET /api/ listing: '.implode(', ', $missing));
    }


    public function testRootListingHasNoPhantomRoutes(): void {
        $router = file_get_contents(__DIR__.'/../../api/router.php');
        preg_match_all('#^\s*\$api->(get|post|put|patch|delete)\(\'(/v1/[^\']*)\'#m', $router, $m, PREG_SET_ORDER);

        $registered = [];
        foreach($m as $route) {
            $registered[] = strtoupper($route[1]).' /api'.$route[2];
        }

        $listing = $this->http('GET', '/api/')['json']['endpoints'] ?? [];

        $phantom = array_diff(array_values($listing), $registered);
        $this->assertSame([], array_values($phantom), 'listed but not registered: '.implode(', ', $phantom));
    }


    public function testRootListingEntriesAreWellFormedAndUnique(): void {
        $listing = $this->http('GET', '/api/')['json']['endpoints'] ?? [];

        foreach($listing as $name => $value) {
            $this->assertTrue(is_string($value) && preg_match('#^(GET|POST|PUT|PATCH|DELETE) /api/v1/\S*$#', $value) === 1, 'bad listing entry '.$name);
        }

        $this->assertSame(count($listing), count(array_unique(array_values($listing))), 'duplicate routes in listing');
    }


    // ---------------------------------------------------------------- health

    public function testHealthShape(): void {
        $response = $this->api('GET', '/v1/health');

        $this->assertApiSuccess($response);
        $this->assertStringContains('application/json', $response['headers']['content-type'] ?? '');

        $data = $this->data($response);
        $this->assertSame('healthy', $data['status'] ?? null);
        $this->assertSame('ok', $data['checks']['api']['status'] ?? null);
        $this->assertSame('healthy', $data['checks']['database']['status'] ?? null);
        $this->assertTrue(($data['checks']['database']['connection_alive'] ?? null) === true, 'database connection must be alive');
        $this->assertTrue(is_string($data['system_info']['timestamp'] ?? null), 'system_info.timestamp is missing');
        $this->assertTrue(is_string($data['system_info']['timezone'] ?? null), 'system_info.timezone is missing');
    }


    public function testHealthDoesNotExposeInternals(): void {
        $body = strtolower($this->api('GET', '/v1/health')['body']);

        $this->assertNoSensitiveData($body);
        $this->assertStringNotContains('php_info', $body);
        $this->assertStringNotContains('version', $body);
    }


    public function testHealthIsReadOnly(): void {
        $this->assertApiError(404, $this->api('POST', '/v1/health', []));
        $this->assertApiError(404, $this->api('DELETE', '/v1/health'));
    }


    public function testHealthDetailsShape(): void {
        $response = $this->api('GET', '/v1/health/details');

        $this->assertApiSuccess($response);
        $data = $this->data($response);

        $this->assertSame('ok', $data['api check']['status'] ?? null);
        $this->assertSame('healthy', $data['database check']['status'] ?? null);
        $this->assertTrue(is_int($data['system_info']['memory_usage']['current'] ?? null), 'memory_usage.current must be int');
        $this->assertTrue(is_int($data['system_info']['memory_usage']['peak'] ?? null), 'memory_usage.peak must be int');
        $this->assertTrue(is_string($data['php_info']['version'] ?? null), 'php_info.version must be string');
        $this->assertTrue(is_array($data['php_info']['configuration'] ?? null), 'php_info.configuration is missing');
        $this->assertTrue(is_string($data['roocms_info']['version'] ?? null), 'roocms_info.version must be string');
        $this->assertTrue(is_int($data['users in system'] ?? null) && $data['users in system'] >= 0, 'users count must be a non-negative int');
    }


    /**
     * The details endpoint is public: it may show versions and counts, but never connection data or filesystem paths
     */
    public function testHealthDetailsDoesNotExposeSecrets(): void {
        $body = strtolower($this->api('GET', '/v1/health/details')['body']);

        $this->assertNoSensitiveData($body);
    }


    private function assertNoSensitiveData(string $lower_body): void {
        foreach(['password', 'passwd', 'secret', 'hash_key', 'token', 'db_host', 'db_user', 'db_name', 'dbhost', 'dbuser', 'dsn', 'stack trace', 'mysql:host'] as $needle) {
            $this->assertStringNotContains($needle, $lower_body, 'response must not contain "'.$needle.'"');
        }

        // absolute filesystem paths (Windows drive or common unix roots; JSON escapes backslashes)
        $this->assertTrue(preg_match('#[a-z]:\\\\|/var/www|/home/|/usr/|/etc/#i', $lower_body) !== 1, 'response must not contain filesystem paths');
    }


    // ---------------------------------------------------------------- CSP report

    public function testCspReportIsAccepted(): void {
        $response = $this->http('POST', '/api/v1/csp-report', json_encode(self::CSP_REPORT), ['Content-Type' => 'application/csp-report']);

        $this->assertApiSuccess($response);
        $this->assertSame('reported', $this->data($response, 'status'));
    }


    public function testCspReportAcceptsJsonContentType(): void {
        $response = $this->api('POST', '/v1/csp-report', self::CSP_REPORT);

        $this->assertApiSuccess($response);
        $this->assertSame('reported', $this->data($response, 'status'));
    }


    public function testCspReportDoesNotRequireAuthentication(): void {
        // browsers send reports without credentials
        $response = $this->http('POST', '/api/v1/csp-report', json_encode(self::CSP_REPORT), ['Content-Type' => 'application/csp-report']);

        $this->assertTrue($response['status'] !== 401 && $response['status'] !== 403, 'CSP report must be public');
    }


    public function testCspReportWithHtmlIsAcceptedAndNotReflected(): void {
        $report = ['csp-report' => ['document-uri' => 'https://dev.roocms.com/', 'blocked-uri' => '<script>alert(1)</script>']];
        $response = $this->api('POST', '/v1/csp-report', $report);

        $this->assertApiSuccess($response);
        $this->assertStringNotContains('<script>', $response['body']);
    }


    public function testCspReportWithoutReportKeyIsRejected(): void {
        $this->assertApiError(400, $this->api('POST', '/v1/csp-report', ['foo' => 'bar']));
    }


    public function testCspReportWithEmptyBodyIsRejected(): void {
        $this->assertApiError(400, $this->http('POST', '/api/v1/csp-report', '', ['Content-Type' => 'application/csp-report']));
        $this->assertApiError(400, $this->http('POST', '/api/v1/csp-report'));
    }


    public function testCspReportWithBrokenJsonIsClientError(): void {
        $response = $this->http('POST', '/api/v1/csp-report', '{broken', ['Content-Type' => 'application/json']);

        $this->assertApiError(400, $response);
    }


    public function testCspReportWithNonObjectJsonIsClientError(): void {
        foreach(['[1,2]', '"text"', '123'] as $body) {
            $response = $this->http('POST', '/api/v1/csp-report', $body, ['Content-Type' => 'application/csp-report']);

            $this->assertApiError(400, $response, 'body '.$body.':');
        }
    }


    /**
     * "csp-report" must be an object; a scalar must be a client error
     */
    public function testCspReportWithScalarReportIsClientError(): void {
        // KNOWN BUG: CspController::log_csp_violation(array $report) gets a string -> TypeError -> generic
        // "internal_error" body is returned with HTTP 200 (also floods error log). Expected: 400
        $response = $this->api('POST', '/v1/csp-report', ['csp-report' => 'not-an-object']);

        $this->assertApiError(400, $response);
    }


    public function testCspReportIsPostOnly(): void {
        $this->assertApiError(404, $this->api('GET', '/v1/csp-report'));
    }


    // ---------------------------------------------------------------- debug endpoints (access control)

    public function testDebugEndpointsRequireToken(): void {
        $this->assertApiError(401, $this->api('GET', '/v1/acp/debug/logs'));
        $this->assertApiError(401, $this->api('POST', '/v1/acp/debug/clear'));
    }


    public function testDebugEndpointsAreForbiddenForRegularUser(): void {
        $user = $this->create_user();

        $logs = $this->api('GET', '/v1/acp/debug/logs', null, $user['token']);
        $this->assertApiError(403, $logs, 'logs:');

        // regular user must not be able to destroy logs
        $clear = $this->api('POST', '/v1/acp/debug/clear', null, $user['token']);
        $this->assertApiError(403, $clear, 'clear:');
        $this->assertStringNotContains('cleared successfully', $clear['body']);
    }


    public function testDebugEndpointsAreForbiddenForModerator(): void {
        // written from the code, needs moderator credentials
        $token = $this->token_for('moderator');

        $this->assertApiError(403, $this->api('GET', '/v1/acp/debug/logs', null, $token), 'logs:');
        $this->assertApiError(403, $this->api('POST', '/v1/acp/debug/clear', null, $token), 'clear:');
    }


    public function testAdminCanReadDebugLogs(): void {
        // written from the code, needs admin credentials. "clear" is intentionally not called: it destroys logs
        $response = $this->api('GET', '/v1/acp/debug/logs', null, $this->token_for('admin'));

        $this->assertApiSuccess($response);
        $logs = $this->data($response, 'logs');
        $this->assertTrue(is_array($logs), 'data.logs must be an array');

        foreach($logs as $entry) {
            $this->assertTrue(is_array($entry) && is_array($entry['debug'] ?? null), 'every log entry must contain a debug array');
        }
    }
}
