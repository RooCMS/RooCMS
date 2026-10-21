<?php declare(strict_types=1);

/**
 * Base class for HTTP tests against a running RooCMS instance
 *
 * Environment:
 *   ROOCMS_TEST_URL                 address of the instance (default https://dev.roocms.com)
 *   ROOCMS_TEST_INSECURE=1          do not verify TLS certificate
 *   ROOCMS_TEST_ADMIN_LOGIN / _PASSWORD        existing test user with role "a" (or "su")
 *   ROOCMS_TEST_MODERATOR_LOGIN / _PASSWORD    existing test user with role "m"
 *
 * The regular user is created by the tests themselves (see create_user()).
 * Everything a test creates is removed in tearDown() through cleanup callbacks.
 */
abstract class ApiTestCase extends TestCase {

    protected string $base_url;

    /** @var array<int, callable> cleanup callbacks, run in reverse order in tearDown() */
    private array $cleanup = [];

    /** @var array<string, array> cached login results by role */
    private static array $sessions = [];


    protected function setUp(): void {
        $this->base_url = rtrim((string)(getenv('ROOCMS_TEST_URL') ?: 'https://dev.roocms.com'), '/');
    }


    protected function tearDown(): void {
        foreach(array_reverse($this->cleanup) as $callback) {
            try {
                $callback();
            } catch(Throwable $e) {
                fwrite(STDERR, '    cleanup failed: '.$e->getMessage()."\n");
            }
        }

        $this->cleanup = [];
    }


    /**
     * Register a callback that removes test data
     */
    protected function on_cleanup(callable $callback): void {
        $this->cleanup[] = $callback;
    }


    /**
     * Raw HTTP request. Skips the test if the server is unreachable
     *
     * @param string|null $body Raw body
     * @return array{status:int, headers:array<string,string>, body:string, json:?array}
     */
    protected function http(string $method, string $path, ?string $body = null, array $headers = []): array {
        $header_lines = [];
        foreach($headers as $name => $value) {
            $header_lines[] = $name.': '.$value;
        }

        $insecure = getenv('ROOCMS_TEST_INSECURE') === '1';

        $context = stream_context_create([
            'http' => [
                'method'          => $method,
                'header'          => implode("\r\n", $header_lines),
                'content'         => $body ?? '',
                'ignore_errors'   => true,
                'timeout'         => 30,
                'follow_location' => 0,
            ],
            'ssl' => [
                'verify_peer'      => !$insecure,
                'verify_peer_name' => !$insecure,
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


    /**
     * API request with JSON body and optional bearer token
     *
     * @param string $path Path after /api, for example "/v1/users/me"
     * @param array|null $json Body, sent as JSON
     * @param string|null $token Access token
     * @return array{status:int, headers:array<string,string>, body:string, json:?array}
     */
    protected function api(string $method, string $path, ?array $json = null, ?string $token = null): array {
        $headers = [];
        $body = null;

        if($json !== null) {
            $headers['Content-Type'] = 'application/json';
            $body = json_encode($json);
        }

        if($token !== null) {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $this->http($method, '/api'.$path, $body, $headers);
    }


    /**
     * Value of data.<key> from API response
     */
    protected function data(array $response, ?string $key = null): mixed {
        $data = $response['json']['data'] ?? null;

        return $key === null ? $data : ($data[$key] ?? null);
    }


    /**
     * Assert HTTP status with the response body in the failure message
     */
    protected function assertStatus(int $expected, array $response, string $context = ''): void {
        if($response['status'] !== $expected) {
            $this->fail(trim($context.' expected HTTP '.$expected.', got '.$response['status'].': '.substr($response['body'], 0, 300)));
        }
    }


    /**
     * Assert standard error response: error=true and given status
     */
    protected function assertApiError(int $status, array $response, string $context = ''): void {
        $this->assertStatus($status, $response, $context);
        $this->assertTrue(($response['json']['error'] ?? false) === true, trim($context.' response must have error=true'));
    }


    /**
     * Assert standard success response: success=true and given status
     */
    protected function assertApiSuccess(array $response, int $status = 200, string $context = ''): void {
        $this->assertStatus($status, $response, $context);
        $this->assertTrue(($response['json']['success'] ?? false) === true, trim($context.' response must have success=true'));
    }


    /**
     * Access token of an existing test user with the given role ("admin" or "moderator")
     * Logs in once per process. Skips the test if credentials are not configured
     */
    protected function token_for(string $role): string {
        return $this->session_for($role)['access_token'];
    }


    /**
     * Full login data (tokens and user) of an existing test user
     */
    protected function session_for(string $role): array {
        if(isset(self::$sessions[$role])) {
            return self::$sessions[$role];
        }

        $prefix = 'ROOCMS_TEST_'.strtoupper($role);
        $login = getenv($prefix.'_LOGIN');
        $password = getenv($prefix.'_PASSWORD');

        if(!$login || !$password) {
            $this->skip('set '.$prefix.'_LOGIN and '.$prefix.'_PASSWORD');
        }

        $response = $this->api('POST', '/v1/auth/login', ['login' => $login, 'password' => $password]);
        $this->assertApiSuccess($response, 200, 'login as '.$role.':');

        self::$sessions[$role] = $this->data($response);

        return self::$sessions[$role];
    }


    /**
     * Fresh login of an existing test user (new token pair, does not touch the cached session)
     * Use it for tests that revoke or rotate tokens
     */
    protected function login_again(string $role): array {
        $prefix = 'ROOCMS_TEST_'.strtoupper($role);

        $response = $this->api('POST', '/v1/auth/login', [
            'login' => getenv($prefix.'_LOGIN'),
            'password' => getenv($prefix.'_PASSWORD'),
        ]);
        $this->assertApiSuccess($response, 200, 'second login as '.$role.':');

        return $this->data($response);
    }


    /**
     * Register a new regular user through the API
     * The user is deleted in tearDown()
     *
     * @return array{id:int, login:string, email:string, password:string, token:string, refresh:string}
     */
    protected function create_user(string $password = 'Test12345!'): array {
        $suffix = bin2hex(random_bytes(4));
        $login = 'test_'.$suffix;
        $email = $login.'@example.invalid';

        $response = $this->api('POST', '/v1/auth/register', [
            'login' => $login,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
        $this->assertApiSuccess($response, 201, 'register '.$login.':');

        $user = [
            'id'       => (int)$this->data($response)['user']['user_id'],
            'login'    => $login,
            'email'    => $email,
            'password' => $password,
            'token'    => $this->data($response)['access_token'],
            'refresh'  => $this->data($response)['refresh_token'],
        ];

        $this->on_cleanup(function() use ($user) {
            // the token can be revoked by the test itself: log in again to delete the account
            $login = $this->api('POST', '/v1/auth/login', ['login' => $user['login'], 'password' => $user['password']]);
            $token = $login['json']['data']['access_token'] ?? $user['token'];

            // DELETE /users/me fails (500) for a user with a non-public profile (new users are such),
            // so make the profile public first. Remove this when the API bug is fixed.
            $this->api('PATCH', '/v1/users/me', ['is_public' => 1], $token);
            $this->api('DELETE', '/v1/users/me', null, $token);
        });

        return $user;
    }
}
