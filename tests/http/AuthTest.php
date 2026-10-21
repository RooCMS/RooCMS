<?php declare(strict_types=1);

/**
 * HTTP tests of authentication endpoints: register, login, refresh, logout, password update/recovery
 *
 * Every user created here is removed in tearDown(). See ApiTestCase for environment variables.
 *
 * NOTE: DELETE /users/me answers 500 for users with a non-public profile (the default state of a new user),
 * so register_user() makes the profile public before deleting the account (see UsersTest::testDeleteMeWorksForDefaultUser).
 */
require_once __DIR__.'/ApiTestCase.php';

class AuthTest extends ApiTestCase {

    /** @var array<string, string[]> extra passwords by login (after password changes), used by cleanup */
    private array $extra_passwords = [];


    /**
     * Register a user and make sure it is really removed afterwards
     */
    private function register_user(string $password = 'Test12345!'): array {
        $user = $this->create_user($password);
        $this->cleanup_user($user['login'], $password);

        return $user;
    }


    /**
     * Remove an account by login: log in (with any known password), drop avatar, make profile public, delete
     */
    private function cleanup_user(string $login, string $password): void {
        $this->on_cleanup(function() use ($login, $password) {
            $passwords = array_merge([$password], $this->extra_passwords[$login] ?? []);
            foreach($passwords as $candidate) {
                $response = $this->api('POST', '/v1/auth/login', ['login' => $login, 'password' => $candidate]);
                $token = $response['json']['data']['access_token'] ?? null;
                if($token === null) {
                    continue;
                }

                $this->api('DELETE', '/v1/users/me/avatar', null, $token);
                $this->api('PATCH', '/v1/users/me', ['is_public' => 1], $token);
                $this->api('DELETE', '/v1/users/me', null, $token);

                return;
            }
        });
    }


    private function login_as(array $user, ?string $password = null): array {
        return $this->api('POST', '/v1/auth/login', ['login' => $user['login'], 'password' => $password ?? $user['password']]);
    }


    /**
     * New independent token pair of the user (second session)
     */
    private function second_session(array $user): array {
        $response = $this->login_as($user);
        $this->assertApiSuccess($response, 200, 'second login:');

        return $this->data($response);
    }


    private function assertTokenWorks(string $token, string $context = ''): void {
        $this->assertStatus(200, $this->api('GET', '/v1/users/me', null, $token), $context.' token must work:');
    }


    private function assertTokenRejected(string $token, string $context = ''): void {
        $this->assertApiError(401, $this->api('GET', '/v1/users/me', null, $token), $context.' token must be rejected:');
    }


    private function assertNoSecrets(array $payload, string $where): void {
        $forbidden = ['password', 'password_hash', 'hash', 'token', 'refresh', 'access_token', 'refresh_token', 'secret'];
        $walk = function(array $node, string $path) use (&$walk, $forbidden, $where) {
            foreach($node as $key => $value) {
                $this->assertFalse(in_array(strtolower((string)$key), $forbidden, true), $where.' must not expose "'.$path.$key.'"');
                if(is_array($value)) {
                    $walk($value, $path.$key.'.');
                }
            }
        };
        $walk($payload, '');
    }


    // ------------------------------------------------------------------ registration


    public function testRegisterSuccessShape(): void {
        $user = $this->register_user();

        // create_user() already asserted 201; check the full shape with a second registration
        $suffix = bin2hex(random_bytes(4));
        $login = 'test_'.$suffix;
        $this->cleanup_user($login, 'Test12345!');
        $response = $this->api('POST', '/v1/auth/register', [
            'login' => $login,
            'email' => $login.'@example.invalid',
            'password' => 'Test12345!',
            'password_confirmation' => 'Test12345!',
        ]);

        $this->assertApiSuccess($response, 201);
        $data = $this->data($response);
        $this->assertTrue(is_string($data['access_token'] ?? null) && $data['access_token'] !== '', 'access_token');
        $this->assertTrue(is_string($data['refresh_token'] ?? null) && $data['refresh_token'] !== '', 'refresh_token');
        $this->assertSame('Bearer', $data['token_type'] ?? null);
        $this->assertTrue(is_int($data['expires_in'] ?? null) && $data['expires_in'] > 0, 'expires_in');
        $this->assertTrue(is_int($data['refresh_expires_in'] ?? null) && $data['refresh_expires_in'] > $data['expires_in'], 'refresh_expires_in');
        $this->assertSame($login, $data['user']['login'] ?? null);
        $this->assertSame($login.'@example.invalid', $data['user']['email'] ?? null);
        $this->assertSame('u', $data['user']['role'] ?? null);
        $this->assertSame(false, $data['user']['is_verified'] ?? null);
        $this->assertTrue(is_int($data['user']['user_id'] ?? null), 'user_id must be int');
        $this->assertNoSecrets($data['user'], 'register user');

        $this->assertTokenWorks($data['access_token'], 'registration');
        $this->assertTrue($user['id'] !== $data['user']['user_id']);
    }


    public function testRegisterWithoutConfirmationFieldIsAllowed(): void {
        $login = 'test_'.bin2hex(random_bytes(4));
        $this->cleanup_user($login, 'Test12345!');

        $response = $this->api('POST', '/v1/auth/register', ['login' => $login, 'email' => $login.'@example.invalid', 'password' => 'Test12345!']);

        $this->assertApiSuccess($response, 201);
    }


    public function testRegisterDuplicateLoginReturns409(): void {
        $user = $this->register_user();

        $response = $this->api('POST', '/v1/auth/register', [
            'login' => $user['login'],
            'email' => 'other_'.bin2hex(random_bytes(4)).'@example.invalid',
            'password' => 'Test12345!',
        ]);

        $this->assertApiError(409, $response);
    }


    public function testRegisterDuplicateEmailReturns409(): void {
        $user = $this->register_user();

        $response = $this->api('POST', '/v1/auth/register', [
            'login' => 'test_'.bin2hex(random_bytes(4)),
            'email' => $user['email'],
            'password' => 'Test12345!',
        ]);

        $this->assertApiError(409, $response);
    }


    public function testRegisterMissingFieldsReturn422(): void {
        $full = ['login' => 'test_'.bin2hex(random_bytes(4)), 'email' => 'x_'.bin2hex(random_bytes(4)).'@example.invalid', 'password' => 'Test12345!'];

        foreach(['login', 'email', 'password'] as $field) {
            $payload = $full;
            unset($payload[$field]);

            $response = $this->api('POST', '/v1/auth/register', $payload);
            $this->assertApiError(422, $response, 'without '.$field.':');
            $this->assertTrue(isset($response['json']['details']['validation_errors'][$field]), 'validation_errors.'.$field.' must be reported');
        }

        $response = $this->api('POST', '/v1/auth/register', ['login' => '', 'email' => '', 'password' => '']);
        $this->assertApiError(422, $response, 'empty strings:');
    }


    public function testRegisterShortPasswordReturns400(): void {
        $login = 'test_'.bin2hex(random_bytes(4));
        $response = $this->api('POST', '/v1/auth/register', ['login' => $login, 'email' => $login.'@example.invalid', 'password' => 'a1']);

        $this->assertApiError(400, $response);
        $this->assertStringContains('at least', (string)($response['json']['message'] ?? ''));
    }


    public function testRegisterPasswordConfirmationMismatch(): void {
        $login = 'test_'.bin2hex(random_bytes(4));
        $response = $this->api('POST', '/v1/auth/register', [
            'login' => $login,
            'email' => $login.'@example.invalid',
            'password' => 'Test12345!',
            'password_confirmation' => 'Different12345!',
        ]);

        $this->assertApiError(422, $response);
        $this->assertTrue(isset($response['json']['details']['validation_errors']['password_confirmation']));

        // the account must not have been created
        $this->assertApiError(401, $this->api('POST', '/v1/auth/login', ['login' => $login, 'password' => 'Test12345!']));
    }


    /**
     * KNOWN BUG: registration does not validate the email format (RegistrationService / validate_registration_data
     * only checks uniqueness), "notanemail" is accepted with 201. Expected 422 (like PATCH /users/me does)
     */
    public function testRegisterRejectsInvalidEmail(): void {
        $login = 'test_'.bin2hex(random_bytes(4));
        $this->cleanup_user($login, 'Test12345!');

        $response = $this->api('POST', '/v1/auth/register', ['login' => $login, 'email' => 'notanemail_'.bin2hex(random_bytes(4)), 'password' => 'Test12345!']);

        $this->assertApiError(422, $response);
    }


    public function testRegisterStripsTagsFromLogin(): void {
        $suffix = bin2hex(random_bytes(4));
        $login = 'test_'.$suffix;
        $this->cleanup_user($login, 'Test12345!');

        $response = $this->api('POST', '/v1/auth/register', [
            'login' => '<b>'.$login.'</b>',
            'email' => $login.'@example.invalid',
            'password' => 'Test12345!',
        ]);

        $this->assertApiSuccess($response, 201);
        $this->assertSame($login, $this->data($response)['user']['login'] ?? null);
    }


    public function testRegisterBrokenJsonIsClientError(): void {
        $response = $this->http('POST', '/api/v1/auth/register', '{"login": "x", ', ['Content-Type' => 'application/json']);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'Got status '.$response['status']);
        $this->assertTrue(($response['json']['error'] ?? false) === true, 'error JSON expected');
    }


    /**
     * KNOWN BUG: array instead of string leads to a TypeError that is not handled by the controller,
     * the client gets the generic internal_error page (HTTP 200 with {"error":"internal_error"}) instead of 4xx
     */
    public function testRegisterArrayValuesAreClientError(): void {
        $response = $this->api('POST', '/v1/auth/register', ['login' => ['a'], 'email' => ['b'], 'password' => ['c']]);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'Got status '.$response['status'].': '.substr($response['body'], 0, 200));
    }


    // ------------------------------------------------------------------ login


    public function testLoginSuccess(): void {
        $user = $this->register_user();

        $response = $this->login_as($user);

        $this->assertApiSuccess($response, 200);
        $data = $this->data($response);
        $this->assertSame('Bearer', $data['token_type'] ?? null);
        $this->assertTrue(is_string($data['access_token'] ?? null) && strlen($data['access_token']) >= 20);
        $this->assertTrue(is_string($data['refresh_token'] ?? null) && strlen($data['refresh_token']) >= 20);
        $this->assertTrue($data['access_token'] !== $user['token'], 'every login must issue a new access token');
        $this->assertSame($user['id'], $data['user']['user_id'] ?? null);
        $this->assertSame($user['login'], $data['user']['login'] ?? null);
        $this->assertSame($user['email'], $data['user']['email'] ?? null);
        $this->assertSame('u', $data['user']['role'] ?? null);
        $this->assertNoSecrets($data['user'], 'login user');

        // the earlier session is independent
        $this->assertTokenWorks($user['token'], 'registration');
        $this->assertTokenWorks($data['access_token'], 'login');
    }


    public function testLoginWrongPasswordAndUnknownUserGiveSameAnswer(): void {
        $user = $this->register_user();

        $wrong = $this->api('POST', '/v1/auth/login', ['login' => $user['login'], 'password' => 'Wrong-password-1']);
        $unknown = $this->api('POST', '/v1/auth/login', ['login' => 'no_such_'.bin2hex(random_bytes(4)), 'password' => 'Wrong-password-1']);

        $this->assertApiError(401, $wrong, 'wrong password:');
        $this->assertApiError(401, $unknown, 'unknown user:');
        $this->assertSame($wrong['json']['message'] ?? null, $unknown['json']['message'] ?? null, 'messages must not reveal which part is wrong');
        $this->assertTrue(!isset($wrong['json']['data']), 'no tokens on failure');
    }


    public function testLoginMissingFieldsReturn422(): void {
        $this->assertApiError(422, $this->api('POST', '/v1/auth/login', ['login' => 'someone']));
        $this->assertApiError(422, $this->api('POST', '/v1/auth/login', ['password' => 'secret123']));
        $this->assertApiError(422, $this->api('POST', '/v1/auth/login', ['login' => '', 'password' => '']));
    }


    public function testLoginBrokenJsonIsClientError(): void {
        $response = $this->http('POST', '/api/v1/auth/login', '{"login":', ['Content-Type' => 'application/json']);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'Got status '.$response['status']);
        $this->assertTrue(($response['json']['error'] ?? false) === true, 'error JSON expected');
    }


    /**
     * KNOWN BUG: login/password given as arrays cause an unhandled TypeError (generic internal_error answer
     * with HTTP 200). Expected a 4xx validation error
     */
    public function testLoginArrayValuesAreClientError(): void {
        $response = $this->api('POST', '/v1/auth/login', ['login' => ['a'], 'password' => ['b']]);
        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'login+password arrays: got '.$response['status'].' '.substr($response['body'], 0, 200));
    }


    public function testLoginArrayPasswordIsClientError(): void {
        $user = $this->register_user();

        $response = $this->api('POST', '/v1/auth/login', ['login' => $user['login'], 'password' => ['Test12345!']]);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'password array: got '.$response['status'].' '.substr($response['body'], 0, 200));
    }


    public function testLoginObjectValuesAreClientError(): void {
        $response = $this->http('POST', '/api/v1/auth/login', '{"login":{"$ne":""},"password":{"$ne":""}}', ['Content-Type' => 'application/json']);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'Got '.$response['status'].' '.substr($response['body'], 0, 200));
    }


    public function testLoginSqlInjectionLikeLoginIsRejected(): void {
        $response = $this->api('POST', '/v1/auth/login', ['login' => "' OR '1'='1", 'password' => "' OR '1'='1"]);

        $this->assertApiError(401, $response);
    }


    // ------------------------------------------------------------------ refresh


    public function testRefreshRotatesTokens(): void {
        $user = $this->register_user();

        $response = $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $user['refresh']]);

        $this->assertApiSuccess($response, 200);
        $data = $this->data($response);
        $this->assertSame('Bearer', $data['token_type'] ?? null);
        $this->assertTrue(($data['access_token'] ?? '') !== '' && $data['access_token'] !== $user['token'], 'new access token expected');
        $this->assertTrue(($data['refresh_token'] ?? '') !== '' && $data['refresh_token'] !== $user['refresh'], 'new refresh token expected');

        // new access works
        $this->assertTokenWorks($data['access_token'], 'new access');

        // the old refresh token is single-use
        $replay = $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $user['refresh']]);
        $this->assertApiError(401, $replay, 'old refresh token:');

        // the old access token is revoked together with its refresh token
        $this->assertTokenRejected($user['token'], 'old access');

        // the new refresh token can be rotated again
        $again = $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $data['refresh_token']]);
        $this->assertApiSuccess($again, 200, 'second rotation:');
    }


    public function testRefreshWithAccessTokenOrGarbageReturns401(): void {
        $user = $this->register_user();

        $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => 'garbage-token']));
        $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => str_repeat('a', 86)]));
        // an access token is not a refresh token
        $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $user['token']]));
        // the session was not damaged by failed attempts
        $this->assertTokenWorks($user['token'], 'untouched session');
    }


    public function testRefreshRequiresToken(): void {
        $this->assertApiError(400, $this->api('POST', '/v1/auth/refresh', []));
        $this->assertApiError(400, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => '']));
    }


    /**
     * KNOWN BUG: refresh_token as array -> unhandled TypeError (generic internal_error with HTTP 200), expected 4xx
     */
    public function testRefreshArrayTokenIsClientError(): void {
        $response = $this->api('POST', '/v1/auth/refresh', ['refresh_token' => ['x']]);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'Got '.$response['status'].' '.substr($response['body'], 0, 200));
    }


    // ------------------------------------------------------------------ logout


    public function testLogoutRevokesAccessAndRefreshToken(): void {
        $user = $this->register_user();
        $other = $this->second_session($user);

        $response = $this->api('POST', '/v1/auth/logout', null, $user['token']);

        $this->assertApiSuccess($response, 200);
        $this->assertTokenRejected($user['token'], 'logged out access');
        $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $user['refresh']]), 'logged out refresh:');

        // another session of the same user is not affected
        $this->assertTokenWorks($other['access_token'], 'other session');
        $this->assertApiSuccess($this->api('POST', '/v1/auth/refresh', ['refresh_token' => $other['refresh_token']]), 200, 'other refresh:');
    }


    public function testLogoutRequiresToken(): void {
        $this->assertApiError(401, $this->api('POST', '/v1/auth/logout'));
        $this->assertApiError(401, $this->api('POST', '/v1/auth/logout', null, 'garbage'));
        $this->assertApiError(401, $this->api('POST', '/v1/auth/logout/all'));
        $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh/revoke', ['refresh_token' => 'x']));
        $this->assertApiError(401, $this->api('PUT', '/v1/auth/password', ['current_password' => 'a', 'new_password' => 'b']));
    }


    public function testLogoutAllRevokesEveryToken(): void {
        $user = $this->register_user();
        $second = $this->second_session($user);
        $third = $this->second_session($user);

        $response = $this->api('POST', '/v1/auth/logout/all', null, $second['access_token']);

        $this->assertApiSuccess($response, 200);
        foreach([$user['token'], $second['access_token'], $third['access_token']] as $i => $token) {
            $this->assertTokenRejected($token, 'session #'.$i);
        }
        foreach([$user['refresh'], $second['refresh_token'], $third['refresh_token']] as $i => $refresh) {
            $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $refresh]), 'refresh #'.$i.':');
        }

        // the account itself is fine: a new login works
        $this->assertApiSuccess($this->login_as($user), 200, 'login after logout/all:');
    }


    public function testLogoutAllDoesNotTouchOtherUsers(): void {
        $first = $this->register_user();
        $second = $this->register_user();

        $this->assertApiSuccess($this->api('POST', '/v1/auth/logout/all', null, $first['token']));

        $this->assertTokenRejected($first['token'], 'first user');
        $this->assertTokenWorks($second['token'], 'second user');
    }


    public function testRevokeRefreshToken(): void {
        $user = $this->register_user();
        $other = $this->second_session($user);

        $response = $this->api('POST', '/v1/auth/refresh/revoke', ['refresh_token' => $other['refresh_token']], $user['token']);

        $this->assertApiSuccess($response, 200);
        $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $other['refresh_token']]), 'revoked refresh:');
        // the current session is still valid
        $this->assertTokenWorks($user['token'], 'current session');
    }


    public function testRevokeRefreshRequiresRefreshToken(): void {
        $user = $this->register_user();

        $this->assertApiError(400, $this->api('POST', '/v1/auth/refresh/revoke', [], $user['token']));
        $this->assertApiError(400, $this->api('POST', '/v1/auth/refresh/revoke', ['refresh_token' => ''], $user['token']));
    }


    /**
     * KNOWN BUG: revoke_refresh_token() ignores the owner: any authenticated user can revoke someone else's
     * refresh token (if the token value is known). Expected: the foreign token stays valid (and the call is rejected or ignored)
     */
    public function testRevokeRefreshCannotAffectForeignToken(): void {
        $owner = $this->register_user();
        $stranger = $this->register_user();

        $this->api('POST', '/v1/auth/refresh/revoke', ['refresh_token' => $owner['refresh']], $stranger['token']);

        $response = $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $owner['refresh']]);
        $this->assertApiSuccess($response, 200, 'refresh token of another user must survive:');
    }


    // ------------------------------------------------------------------ password update


    public function testUpdatePasswordValidation(): void {
        $user = $this->register_user();

        // missing fields
        $this->assertApiError(422, $this->api('PUT', '/v1/auth/password', ['new_password' => 'NewPass12345!'], $user['token']));
        $this->assertApiError(422, $this->api('PUT', '/v1/auth/password', ['current_password' => $user['password']], $user['token']));

        // wrong current password
        $response = $this->api('PUT', '/v1/auth/password', ['current_password' => 'Wrong12345!', 'new_password' => 'NewPass12345!'], $user['token']);
        $this->assertApiError(400, $response, 'wrong current password:');

        // too short
        $response = $this->api('PUT', '/v1/auth/password', ['current_password' => $user['password'], 'new_password' => 'a1'], $user['token']);
        $this->assertApiError(400, $response, 'short new password:');

        // confirmation mismatch
        $response = $this->api('PUT', '/v1/auth/password', [
            'current_password' => $user['password'],
            'new_password' => 'NewPass12345!',
            'new_password_confirmation' => 'Mismatch12345!',
        ], $user['token']);
        $this->assertApiError(400, $response, 'confirmation mismatch:');

        // nothing changed: old password and session still work
        $this->assertApiSuccess($this->login_as($user), 200, 'old password after failed updates:');
        $this->assertTokenWorks($user['token'], 'session after failed updates');
    }


    public function testUpdatePasswordRevokesTokensAndChangesPassword(): void {
        $user = $this->register_user();
        $other = $this->second_session($user);
        $new_password = 'NewPass12345!';
        $this->extra_passwords[$user['login']][] = $new_password;

        $response = $this->api('PUT', '/v1/auth/password', [
            'current_password' => $user['password'],
            'new_password' => $new_password,
            'new_password_confirmation' => $new_password,
        ], $user['token']);

        $this->assertApiSuccess($response, 200);

        // every old token is gone, including the one that made the request
        $this->assertTokenRejected($user['token'], 'requesting access');
        $this->assertTokenRejected($other['access_token'], 'other access');
        $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $user['refresh']]), 'old refresh:');
        $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $other['refresh_token']]), 'other refresh:');

        // old password no longer works, the new one does
        $this->assertApiError(401, $this->login_as($user), 'old password:');
        $login = $this->login_as($user, $new_password);
        $this->assertApiSuccess($login, 200, 'new password:');
        $this->assertTokenWorks($this->data($login)['access_token'], 'token of new login');
    }


    public function testUpdatePasswordArrayValuesAreClientError(): void {
        $user = $this->register_user();

        // KNOWN BUG: arrays cause unhandled TypeError instead of a 4xx answer
        $response = $this->api('PUT', '/v1/auth/password', ['current_password' => ['a'], 'new_password' => ['b']], $user['token']);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'Got '.$response['status'].' '.substr($response['body'], 0, 200));
    }


    // ------------------------------------------------------------------ password recovery


    public function testRecoveryAlwaysAnswers200ForUnknownEmail(): void {
        $unknown = $this->api('POST', '/v1/auth/password/recovery', ['email' => 'nobody_'.bin2hex(random_bytes(6)).'@example.invalid']);
        $this->assertApiSuccess($unknown, 200, 'unknown email:');
        $this->assertTrue(!isset($unknown['json']['data']['recovery_code']), 'no code for unknown email');
    }


    public function testRecoveryAnswersAreIndistinguishable(): void {
        $user = $this->register_user();

        $known = $this->api('POST', '/v1/auth/password/recovery', ['email' => $user['email']]);
        $unknown = $this->api('POST', '/v1/auth/password/recovery', ['email' => 'nobody_'.bin2hex(random_bytes(6)).'@example.invalid']);

        $this->assertApiSuccess($known, 200);
        $this->assertSame($known['json']['message'] ?? null, $unknown['json']['message'] ?? null, 'message must not reveal existence');
    }


    public function testRecoveryValidation(): void {
        $this->assertApiError(400, $this->api('POST', '/v1/auth/password/recovery', []));
        $this->assertApiError(400, $this->api('POST', '/v1/auth/password/recovery', ['email' => '']));
        $this->assertApiError(400, $this->api('POST', '/v1/auth/password/recovery', ['email' => 'not-an-email']));
    }


    /**
     * KNOWN BUG: email as array -> unhandled TypeError (generic internal_error), expected 4xx
     */
    public function testRecoveryArrayEmailIsClientError(): void {
        $response = $this->api('POST', '/v1/auth/password/recovery', ['email' => ['a@example.invalid']]);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'Got '.$response['status'].' '.substr($response['body'], 0, 200));
    }


    public function testResetPasswordWithWrongCodeReturns400(): void {
        $response = $this->api('POST', '/v1/auth/password/reset', ['token' => '000000', 'password' => 'NewPass12345!']);
        $this->assertApiError(400, $response, 'wrong code:');

        $response = $this->api('POST', '/v1/auth/password/reset', ['token' => 'zzz-garbage', 'password' => 'NewPass12345!', 'password_confirmation' => 'NewPass12345!']);
        $this->assertApiError(400, $response, 'garbage code:');
    }


    public function testResetPasswordValidation(): void {
        $this->assertApiError(422, $this->api('POST', '/v1/auth/password/reset', ['password' => 'NewPass12345!']));
        $this->assertApiError(422, $this->api('POST', '/v1/auth/password/reset', ['token' => '123456']));
        $this->assertApiError(400, $this->api('POST', '/v1/auth/password/reset', [
            'token' => '123456',
            'password' => 'NewPass12345!',
            'password_confirmation' => 'Mismatch12345!',
        ]));
    }


    /**
     * KNOWN BUG: token/password as arrays -> unhandled TypeError, expected 4xx
     */
    public function testResetPasswordArrayValuesAreClientError(): void {
        $response = $this->api('POST', '/v1/auth/password/reset', ['token' => ['1'], 'password' => ['2']]);

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'Got '.$response['status'].' '.substr($response['body'], 0, 200));
    }


    /**
     * Full flow. The code is returned in the response only when the server runs with DEBUGMODE,
     * otherwise it is sent by email and the test is skipped
     */
    public function testRecoveryAndResetFlow(): void {
        $user = $this->register_user();
        $new_password = 'Recovered12345!';
        $this->extra_passwords[$user['login']][] = $new_password;

        $request = $this->api('POST', '/v1/auth/password/recovery', ['email' => $user['email']]);
        $this->assertApiSuccess($request, 200);

        $code = $this->data($request, 'recovery_code');
        if($code === null) {
            $this->skip('recovery code is not exposed (server is not in DEBUGMODE)');
        }
        $code = (string)$code;

        // weak passwords are rejected and do not burn the code
        $this->assertApiError(400, $this->api('POST', '/v1/auth/password/reset', ['token' => $code, 'password' => 'a1']), 'short password:');
        $this->assertApiError(400, $this->api('POST', '/v1/auth/password/reset', ['token' => $code, 'password' => 'onlyletterspassword']), 'no digits:');
        $this->assertApiError(400, $this->api('POST', '/v1/auth/password/reset', ['token' => $code, 'password' => '1234567890123']), 'no letters:');

        $reset = $this->api('POST', '/v1/auth/password/reset', ['token' => $code, 'password' => $new_password, 'password_confirmation' => $new_password]);
        $this->assertApiSuccess($reset, 200, 'reset:');

        // all sessions are revoked, the old password is dead, the new one works
        $this->assertTokenRejected($user['token'], 'old session');
        $this->assertApiError(401, $this->login_as($user), 'old password:');
        $this->assertApiSuccess($this->login_as($user, $new_password), 200, 'new password:');

        // the code is single-use
        $replay = $this->api('POST', '/v1/auth/password/reset', ['token' => $code, 'password' => 'Another12345!']);
        $this->assertApiError(400, $replay, 'code reuse:');
        $this->assertApiError(401, $this->login_as($user, 'Another12345!'), 'password must not change on reuse:');
    }


    // ------------------------------------------------------------------ misc


    public function testProfileHidesSecrets(): void {
        $user = $this->register_user();

        $response = $this->api('GET', '/v1/users/me', null, $user['token']);

        $this->assertApiSuccess($response);
        $this->assertNoSecrets($this->data($response), 'GET /users/me');
        $this->assertStringNotContains('$2y$', $response['body']);
    }


    public function testTokenFormatsAreRejected(): void {
        foreach(['', ' ', 'null', 'undefined', str_repeat('A', 5000), "a'b", '../../etc/passwd'] as $token) {
            $response = $this->http('GET', '/api/v1/users/me', null, ['Authorization' => 'Bearer '.$token]);
            $this->assertTrue($response['status'] === 401, 'token "'.substr($token, 0, 20).'" must give 401, got '.$response['status']);
        }

        // not a Bearer scheme
        $user = $this->register_user();
        $response = $this->http('GET', '/api/v1/users/me', null, ['Authorization' => 'Basic '.$user['token']]);
        $this->assertSame(401, $response['status']);
    }
}
