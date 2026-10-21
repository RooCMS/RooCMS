<?php declare(strict_types=1);

/**
 * HTTP tests of user endpoints: list, profile, update, delete, email verification, avatar
 *
 * Every user created here is removed in tearDown(). Tests that need an admin or moderator
 * (token_for) are skipped without ROOCMS_TEST_ADMIN_* / ROOCMS_TEST_MODERATOR_* variables.
 *
 * NOTE: DELETE /users/me answers 500 for users with a non-public profile (the default state of a new user),
 * so register_user() makes the profile public before deleting the account (see testDeleteMeWorksForDefaultUser).
 */
require_once __DIR__.'/ApiTestCase.php';

class UsersTest extends ApiTestCase {

    /** 1x1 transparent PNG */
    private const PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';


    /**
     * Register a user and make sure it is really removed afterwards
     */
    private function register_user(string $password = 'Test12345!'): array {
        $user = $this->create_user($password);
        $login = $user['login'];

        $this->on_cleanup(function() use ($login, $password) {
            $response = $this->api('POST', '/v1/auth/login', ['login' => $login, 'password' => $password]);
            $token = $response['json']['data']['access_token'] ?? null;
            if($token === null) {
                return;
            }

            $this->api('DELETE', '/v1/users/me/avatar', null, $token);
            $this->api('PATCH', '/v1/users/me', ['is_public' => 1], $token);
            $this->api('DELETE', '/v1/users/me', null, $token);
        });

        return $user;
    }


    private function me(string $token): array {
        $response = $this->api('GET', '/v1/users/me', null, $token);
        $this->assertApiSuccess($response, 200, 'GET /users/me:');

        return $this->data($response);
    }


    private function patch_me(string $token, array $payload): array {
        return $this->api('PATCH', '/v1/users/me', $payload, $token);
    }


    /**
     * Multipart body with one file field
     *
     * @return array{0:string, 1:array<string,string>}
     */
    private function multipart(string $field, string $filename, string $content, string $mime, string $token): array {
        $boundary = '----RooCMSTest'.bin2hex(random_bytes(8));
        $body = '--'.$boundary."\r\n"
            .'Content-Disposition: form-data; name="'.$field.'"; filename="'.$filename.'"'."\r\n"
            .'Content-Type: '.$mime."\r\n\r\n"
            .$content."\r\n"
            .'--'.$boundary.'--'."\r\n";

        return [$body, [
            'Content-Type' => 'multipart/form-data; boundary='.$boundary,
            'Authorization' => 'Bearer '.$token,
        ]];
    }


    private function upload_avatar(string $token, string $filename, string $content, string $mime, string $field = 'avatar'): array {
        [$body, $headers] = $this->multipart($field, $filename, $content, $mime, $token);

        return $this->http('POST', '/api/v1/users/me/avatar', $body, $headers);
    }


    // ------------------------------------------------------------------ list


    public function testListRequiresAuthentication(): void {
        $this->assertApiError(401, $this->api('GET', '/v1/users'));
        $this->assertApiError(401, $this->api('GET', '/v1/users/1'));
        $this->assertApiError(401, $this->api('GET', '/v1/users/me'));
        $this->assertApiError(401, $this->api('GET', '/v1/users', null, 'garbage'));
    }


    public function testListShapeAndPagination(): void {
        $user = $this->register_user();

        $response = $this->api('GET', '/v1/users', null, $user['token']);

        $this->assertApiSuccess($response);
        $data = $this->data($response);
        $this->assertTrue(is_array($data['items'] ?? null), 'items');
        foreach(['current_page', 'per_page', 'total', 'total_pages', 'has_next', 'has_prev'] as $key) {
            $this->assertTrue(array_key_exists($key, $data['meta'] ?? []), 'meta.'.$key);
        }
        $this->assertSame(1, $data['meta']['current_page']);
        $this->assertFalse($data['meta']['has_prev']);
        $this->assertTrue(count($data['items']) <= $data['meta']['per_page']);

        // limits are clamped to 10..100
        $this->assertSame(100, $this->data($this->api('GET', '/v1/users?limit=100000', null, $user['token']))['meta']['per_page']);
        $this->assertSame(10, $this->data($this->api('GET', '/v1/users?limit=1', null, $user['token']))['meta']['per_page']);
        $this->assertSame(1, $this->data($this->api('GET', '/v1/users?page=-5', null, $user['token']))['meta']['current_page']);
    }


    public function testListNeverExposesPasswordsOrTokens(): void {
        $user = $this->register_user();

        $response = $this->api('GET', '/v1/users?limit=100', null, $user['token']);

        $this->assertApiSuccess($response);
        foreach($this->data($response, 'items') as $item) {
            foreach(['password', 'token', 'refresh'] as $key) {
                $this->assertFalse(array_key_exists($key, $item), 'item must not contain '.$key);
            }
        }
        $this->assertStringNotContains('$2y$', $response['body']);
    }


    public function testListSearchFindsUserAndSqlWildcardsAreSafe(): void {
        $user = $this->register_user();

        $found = $this->api('GET', '/v1/users?search='.rawurlencode($user['login']), null, $user['token']);
        $this->assertApiSuccess($found);
        $ids = array_map(fn($item) => (int)$item['id'], $this->data($found, 'items'));
        $this->assertTrue(in_array($user['id'], $ids, true), 'own account must be found by login');

        $none = $this->api('GET', '/v1/users?search='.rawurlencode('no_such_user_'.bin2hex(random_bytes(6))), null, $user['token']);
        $this->assertApiSuccess($none);
        $this->assertSame(0, $this->data($none)['meta']['total']);

        $injection = $this->api('GET', '/v1/users?search='.rawurlencode("' OR 1=1 -- "), null, $user['token']);
        $this->assertApiSuccess($injection);
        $this->assertSame(0, $this->data($injection)['meta']['total']);
    }


    public function testListHidesDeletedUsersFromRegularUsers(): void {
        $user = $this->register_user();

        // regular users cannot ask for deleted accounts
        $response = $this->api('GET', '/v1/users?is_deleted=1&limit=100', null, $user['token']);

        $this->assertApiSuccess($response);
        foreach($this->data($response, 'items') as $item) {
            $this->assertTrue((string)($item['is_deleted'] ?? '0') !== '1', 'deleted accounts must not be listed');
        }
        // is_deleted=1 is ignored for regular users: the list equals the default list
        $default = $this->api('GET', '/v1/users', null, $user['token']);
        $this->assertSame($this->data($default)['meta']['total'], $this->data($response)['meta']['total'], 'is_deleted filter must be ignored for role u');
    }


    /**
     * GET /users: e-mails are visible only to moderators and admins.
     * (ban_reason / ban_expired are public by design and are not checked here)
     */
    public function testListDoesNotExposeEmailsToRegularUsers(): void {
        $viewer = $this->register_user();
        $other = $this->register_user();

        $response = $this->api('GET', '/v1/users?search='.rawurlencode($other['login']), null, $viewer['token']);
        $this->assertApiSuccess($response);

        $this->assertTrue(count($this->data($response, 'items')) > 0, 'the search by login must find the user');
        foreach($this->data($response, 'items') as $item) {
            $this->assertFalse(array_key_exists('email', $item), 'email must not be listed for regular users');
        }
    }


    public function testListSearchByEmailIsNotAvailableToRegularUsers(): void {
        $viewer = $this->register_user();
        $other = $this->register_user();

        // otherwise e-mails could be guessed through the search even though they are hidden
        $response = $this->api('GET', '/v1/users?search='.rawurlencode($other['email']), null, $viewer['token']);
        $this->assertApiSuccess($response);

        foreach($this->data($response, 'items') as $item) {
            $this->assertTrue((int)$item['id'] !== $other['id'], 'a user must not be found by e-mail');
        }
    }

    // ------------------------------------------------------------------ me


    public function testMeReturnsOwnProfile(): void {
        $user = $this->register_user();

        $me = $this->me($user['token']);

        $this->assertSame($user['id'], (int)$me['id']);
        $this->assertSame($user['login'], $me['login']);
        $this->assertSame($user['email'], $me['email']);
        $this->assertSame('u', $me['role']);
        $this->assertSame('0', (string)$me['is_verified']);
        $this->assertSame('1', (string)$me['is_active']);
        $this->assertSame('0', (string)$me['is_banned']);
        $this->assertTrue(!isset($me['avatar']) || $me['avatar'] === '', 'new user has no avatar');
        foreach(['password', 'password_hash', 'token', 'refresh'] as $key) {
            $this->assertFalse(array_key_exists($key, $me), 'must not expose '.$key);
        }
    }


    public function testShowOwnProfileEvenWhenPrivate(): void {
        $user = $this->register_user();

        $response = $this->api('GET', '/v1/users/'.$user['id'], null, $user['token']);

        $this->assertApiSuccess($response);
        $this->assertSame($user['email'], $this->data($response, 'email'));
        $this->assertSame($user['login'], $this->data($response, 'login'));
    }


    // ------------------------------------------------------------------ update me


    public function testUpdateMeStoresAllowedFields(): void {
        $user = $this->register_user();
        $nickname = 'nick'.bin2hex(random_bytes(4));

        $response = $this->patch_me($user['token'], [
            'nickname' => $nickname,
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'bio' => 'Hello there',
            'gender' => 'male',
            'birthday' => '1990-05-17',
            'website' => 'https://example.com/path?x=1',
            'is_public' => 1,
        ]);

        $this->assertApiSuccess($response, 200);

        $me = $this->me($user['token']);
        $this->assertSame($nickname, $me['nickname']);
        $this->assertSame('Ivan', $me['first_name']);
        $this->assertSame('Petrov', $me['last_name']);
        $this->assertSame('Hello there', $me['bio']);
        $this->assertSame('male', $me['gender']);
        $this->assertSame('1990-05-17', $me['birthday']);
        $this->assertSame('https://example.com/path?x=1', $me['website']);
        $this->assertSame('1', (string)$me['is_public']);
    }


    public function testUpdateMeRequiresValidFields(): void {
        $user = $this->register_user();

        $this->assertApiError(400, $this->patch_me($user['token'], []), 'empty body:');
        $this->assertApiError(400, $this->patch_me($user['token'], ['unknown_field' => 'x']), 'unknown field:');
        $this->assertApiError(400, $this->patch_me($user['token'], ['role' => 'a']), 'only forbidden field:');
        $this->assertApiError(401, $this->api('PATCH', '/v1/users/me', ['bio' => 'x']), 'no token:');
    }


    public function testUpdateMeStripsTags(): void {
        $user = $this->register_user();
        $nickname = 'nk'.bin2hex(random_bytes(4));

        $response = $this->patch_me($user['token'], [
            'nickname' => '<b>'.$nickname.'</b>',
            'first_name' => '<script>alert(1)</script>Ivan',
            'bio' => '<i>bio</i> <img src=x onerror=alert(1)>text',
        ]);

        $this->assertApiSuccess($response);
        $me = $this->me($user['token']);
        $this->assertSame($nickname, $me['nickname']);
        $this->assertStringNotContains('<', (string)$me['first_name']);
        $this->assertStringNotContains('<', (string)$me['bio']);
        $this->assertStringContains('Ivan', (string)$me['first_name']);
        $this->assertStringContains('text', (string)$me['bio']);
    }


    public function testUpdateMeIgnoresNonWhitelistedFields(): void {
        $user = $this->register_user();

        $response = $this->patch_me($user['token'], [
            'bio' => 'allowed',
            'role' => 'a',
            'is_active' => 0,
            'is_banned' => 1,
            'is_verified' => 1,
            'is_deleted' => 1,
            'login' => 'hacked_'.bin2hex(random_bytes(3)),
            'password' => 'Hacked12345!',
            'id' => 1,
            'ban_expired' => time() + 99999,
        ]);

        $this->assertApiSuccess($response);

        $me = $this->me($user['token']);
        $this->assertSame('allowed', $me['bio']);
        $this->assertSame('u', $me['role'], 'role must not be changed through PATCH /users/me');
        $this->assertSame('1', (string)$me['is_active']);
        $this->assertSame('0', (string)$me['is_banned']);
        $this->assertSame('0', (string)$me['is_verified']);
        $this->assertSame($user['login'], $me['login']);
        $this->assertSame($user['id'], (int)$me['id']);

        // the password did not change
        $this->assertApiSuccess($this->api('POST', '/v1/auth/login', ['login' => $user['login'], 'password' => $user['password']]), 200, 'old password:');
        $this->assertApiError(401, $this->api('POST', '/v1/auth/login', ['login' => $user['login'], 'password' => 'Hacked12345!']), 'injected password:');
    }


    public function testUpdateMeWebsiteAcceptsOnlyHttpAndHttps(): void {
        $user = $this->register_user();

        foreach(['javascript:alert(1)', 'ftp://example.com/file', 'data:text/html,hello', 'file:///etc/passwd', 'example.com', 'not a url'] as $website) {
            $response = $this->patch_me($user['token'], ['website' => $website]);
            $this->assertApiError(422, $response, 'website "'.$website.'":');
        }

        foreach(['https://example.com', 'http://example.com/a/b?c=d#e'] as $website) {
            $this->assertApiSuccess($this->patch_me($user['token'], ['website' => $website]), 200, 'website "'.$website.'":');
            $this->assertSame($website, $this->me($user['token'])['website']);
        }

        // empty value clears the website
        $this->assertApiSuccess($this->patch_me($user['token'], ['website' => '']), 200);
        $this->assertTrue(empty($this->me($user['token'])['website']), 'website must be cleared');
    }


    public function testUpdateMeGender(): void {
        $user = $this->register_user();

        $this->assertApiError(422, $this->patch_me($user['token'], ['gender' => 'robot']), 'invalid gender:');
        $this->assertApiError(422, $this->patch_me($user['token'], ['gender' => 'm']), 'abbreviation:');

        foreach(['male', 'female', 'other'] as $gender) {
            $this->assertApiSuccess($this->patch_me($user['token'], ['gender' => $gender]), 200, $gender.':');
            $this->assertSame($gender, $this->me($user['token'])['gender']);
        }

        // case-insensitive input is normalised
        $this->assertApiSuccess($this->patch_me($user['token'], ['gender' => 'FEMALE']), 200);
        $this->assertSame('female', $this->me($user['token'])['gender']);
    }


    public function testUpdateMeBirthday(): void {
        $user = $this->register_user();

        foreach(['not-a-date', '31.12.2000', '2000/12/31', '2000-1-1x', '2000-12-31 10:00'] as $birthday) {
            $this->assertApiError(422, $this->patch_me($user['token'], ['birthday' => $birthday]), 'birthday "'.$birthday.'":');
        }

        $this->assertApiSuccess($this->patch_me($user['token'], ['birthday' => '2000-12-31']), 200);
        $this->assertSame('2000-12-31', $this->me($user['token'])['birthday']);
    }


    /**
     * KNOWN BUG: validate_birthday() only checks parse errors, but date_create_from_format() reports
     * impossible dates (month 13, 31 February) as warnings, so they reach the database: "2000-13-45" gives HTTP 500
     * with the raw SQL error text in the message (information disclosure), "2001-02-31" is probably silently
     * accepted. Expected 422
     */
    public function testUpdateMeRejectsImpossibleBirthday(): void {
        $user = $this->register_user();

        foreach(['2000-13-45', '2001-02-31'] as $birthday) {
            $this->assertApiError(422, $this->patch_me($user['token'], ['birthday' => $birthday]), 'birthday "'.$birthday.'":');
        }
    }


    /**
     * KNOWN BUG: changing the email to an address of another user with a private profile (default) returns 200:
     * UserValidationService::validate_email() looks the owner up through the public getter (p.is_public = 1),
     * so the uniqueness check is bypassed and two accounts get the same email. Expected 409
     */
    public function testUpdateMeEmail(): void {
        $user = $this->register_user();
        $other = $this->register_user();

        $this->assertApiError(422, $this->patch_me($user['token'], ['email' => 'not-an-email']), 'invalid email:');
        $this->assertApiError(409, $this->patch_me($user['token'], ['email' => $other['email']]), 'duplicate email:');

        // own email is allowed
        $this->assertApiSuccess($this->patch_me($user['token'], ['email' => $user['email']]), 200);

        $new = 'new_'.bin2hex(random_bytes(4)).'@example.invalid';
        $this->assertApiSuccess($this->patch_me($user['token'], ['email' => $new]), 200);
        $this->assertSame($new, $this->me($user['token'])['email']);
    }


    public function testUpdateMeDuplicateNicknameReturns409(): void {
        $first = $this->register_user();
        $second = $this->register_user();
        $nickname = 'dup'.bin2hex(random_bytes(4));

        $this->assertApiSuccess($this->patch_me($first['token'], ['nickname' => $nickname]), 200);

        $this->assertApiError(409, $this->patch_me($second['token'], ['nickname' => $nickname]), 'duplicate nickname:');
        // the owner may save the same nickname again
        $this->assertApiSuccess($this->patch_me($first['token'], ['nickname' => $nickname, 'bio' => 'same']), 200, 'own nickname:');
    }


    public function testUpdateMeBrokenJsonAndArrays(): void {
        $user = $this->register_user();

        $response = $this->http('PATCH', '/api/v1/users/me', '{"bio": ', ['Content-Type' => 'application/json', 'Authorization' => 'Bearer '.$user['token']]);
        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'broken JSON: got '.$response['status']);
    }


    /**
     * KNOWN BUG: arrays instead of strings in profile fields are not rejected: nickname ["x"] answers
     * 200 and is stored as the string "Array" (cast with a PHP warning). Expected a 4xx for every field
     */
    public function testUpdateMeArrayValuesAreClientError(): void {
        $user = $this->register_user();

        foreach(['nickname', 'website', 'gender', 'birthday', 'email'] as $field) {
            $response = $this->patch_me($user['token'], [$field => ['x']]);
            $this->assertTrue(
                $response['status'] >= 400 && $response['status'] < 500,
                $field.' as array: got '.$response['status'].' '.substr($response['body'], 0, 150)
            );
        }
    }


    /**
     * KNOWN BUG: "avatar" is in the PATCH /users/me whitelist and accepts an arbitrary string (URL, path with ../).
     * The avatar must only be set through POST /users/me/avatar. Worse, DELETE /users/me/avatar later
     * unlinks _UPLOAD/<stored value>, so a path traversal value can delete arbitrary files (not exercised here)
     */
    public function testUpdateMeCannotSetArbitraryAvatar(): void {
        $user = $this->register_user();
        $value = 'https://example.invalid/evil.png';

        $this->patch_me($user['token'], ['avatar' => $value]);

        $this->assertTrue(($this->me($user['token'])['avatar'] ?? null) !== $value, 'arbitrary avatar value must not be stored');
    }


    // ------------------------------------------------------------------ public profile


    public function testShowMissingUserReturns404(): void {
        $user = $this->register_user();

        $this->assertApiError(404, $this->api('GET', '/v1/users/2147483000', null, $user['token']));
        $this->assertApiError(404, $this->api('GET', '/v1/users/0', null, $user['token']));
    }


    public function testShowNonNumericIdIsNotAServerError(): void {
        $user = $this->register_user();

        foreach(['abc', '1abc', '-1', '1.5', '%27%20OR%201=1'] as $id) {
            $response = $this->api('GET', '/v1/users/'.$id, null, $user['token']);
            $this->assertTrue($response['status'] === 404 || $response['status'] === 400, 'id "'.$id.'": got '.$response['status'].' '.substr($response['body'], 0, 150));
        }
    }


    public function testPrivateProfileIsHiddenFromOtherUsers(): void {
        $owner = $this->register_user();
        $viewer = $this->register_user();

        // is_public = 0 by default
        $response = $this->api('GET', '/v1/users/'.$owner['id'], null, $viewer['token']);

        $this->assertApiError(404, $response);
    }


    public function testPublicProfileIsVisibleWithoutPrivateData(): void {
        $owner = $this->register_user();
        $viewer = $this->register_user();
        $nickname = 'pub'.bin2hex(random_bytes(4));

        $this->assertApiSuccess($this->patch_me($owner['token'], ['is_public' => 1, 'nickname' => $nickname, 'bio' => 'public bio']), 200);

        $response = $this->api('GET', '/v1/users/'.$owner['id'], null, $viewer['token']);

        $this->assertApiSuccess($response);
        $data = $this->data($response);
        $this->assertSame($owner['id'], (int)$data['id']);
        $this->assertSame($nickname, $data['nickname']);
        $this->assertSame('public bio', $data['bio']);
        foreach(['email', 'login', 'password', 'password_hash', 'token'] as $key) {
            $this->assertFalse(array_key_exists($key, $data), 'public profile must not contain '.$key);
        }

        // and it disappears again when the owner makes it private
        $this->assertApiSuccess($this->patch_me($owner['token'], ['is_public' => 0]), 200);
        $this->assertApiError(404, $this->api('GET', '/v1/users/'.$owner['id'], null, $viewer['token']));
    }


    public function testAdminSeesPrivateProfileWithFullData(): void {
        $admin = $this->token_for('admin');
        $owner = $this->register_user(); // private profile

        $response = $this->api('GET', '/v1/users/'.$owner['id'], null, $admin);

        $this->assertApiSuccess($response);
        $this->assertSame($owner['email'], $this->data($response, 'email'));
        $this->assertSame($owner['login'], $this->data($response, 'login'));
        $this->assertFalse(array_key_exists('password', $this->data($response)), 'password hash must never be returned');
    }


    public function testModeratorDoesNotSeePrivateProfile(): void {
        $moderator = $this->token_for('moderator');
        $owner = $this->register_user();

        // UserService::get_user gives full access only to the owner and to roles a/su
        $this->assertApiError(404, $this->api('GET', '/v1/users/'.$owner['id'], null, $moderator));
    }


    public function testAdminCanFilterListByDeleted(): void {
        $admin = $this->token_for('admin');

        $response = $this->api('GET', '/v1/users?is_deleted=1&limit=100', null, $admin);

        $this->assertApiSuccess($response);
        foreach($this->data($response, 'items') as $item) {
            $this->assertSame('1', (string)($item['is_deleted'] ?? '1'), 'only deleted users expected');
        }
    }


    // ------------------------------------------------------------------ email verification


    public function testVerifyEmailWithGarbageCodeReturns400(): void {
        foreach(['garbage', str_repeat('a', 40), '000000', "a'b"] as $code) {
            $response = $this->api('GET', '/v1/users/verify-email/'.rawurlencode($code));
            $this->assertApiError(400, $response, 'code "'.$code.'":');
        }
    }


    public function testRequestVerifyEmailRequiresAuthentication(): void {
        $this->assertApiError(401, $this->api('POST', '/v1/users/me/verify-email'));
    }


    /**
     * The code is returned in the response only when the server runs with DEBUGMODE, otherwise it is sent
     * by email and the test is skipped after checking the request itself
     */
    public function testRequestAndConfirmEmailVerification(): void {
        $user = $this->register_user();

        $response = $this->api('POST', '/v1/users/me/verify-email', null, $user['token']);
        $this->assertApiSuccess($response, 200);

        $code = $this->data($response, 'verification_code');
        if($code === null) {
            $this->skip('verification code is not exposed (server is not in DEBUGMODE)');
        }

        $this->assertSame('0', (string)$this->me($user['token'])['is_verified']);

        $verify = $this->api('GET', '/v1/users/verify-email/'.rawurlencode((string)$code));
        $this->assertApiSuccess($verify, 200, 'verify:');
        $this->assertSame('1', (string)$this->me($user['token'])['is_verified']);

        // the code is single-use and a verified user cannot request a new one
        $this->assertApiError(400, $this->api('GET', '/v1/users/verify-email/'.rawurlencode((string)$code)), 'code reuse:');
        $this->assertApiError(400, $this->api('POST', '/v1/users/me/verify-email', null, $user['token']), 'already verified:');
    }


    /**
     * A verification code issued for one email must not verify a different address after the email was changed
     * KNOWN BUG (by code reading, not executed): PATCH /users/me email change does not reset is_verified nor invalidate pending codes
     */
    public function testChangingEmailResetsVerification(): void {
        $user = $this->register_user();

        $response = $this->api('POST', '/v1/users/me/verify-email', null, $user['token']);
        $this->assertApiSuccess($response, 200);
        $code = $this->data($response, 'verification_code');
        if($code === null) {
            $this->skip('verification code is not exposed (server is not in DEBUGMODE)');
        }

        $this->assertApiSuccess($this->api('GET', '/v1/users/verify-email/'.rawurlencode((string)$code)), 200);
        $this->assertSame('1', (string)$this->me($user['token'])['is_verified']);

        $new = 'changed_'.bin2hex(random_bytes(4)).'@example.invalid';
        $this->assertApiSuccess($this->patch_me($user['token'], ['email' => $new]), 200);

        $this->assertSame('0', (string)$this->me($user['token'])['is_verified'], 'a new e-mail address is not verified');
    }


    // ------------------------------------------------------------------ delete me


    public function testDeleteMeRevokesAccess(): void {
        $user = $this->register_user();

        // work around the default-profile bug, see testDeleteMeWorksForDefaultUser
        $this->assertApiSuccess($this->patch_me($user['token'], ['is_public' => 1]), 200);

        $response = $this->api('DELETE', '/v1/users/me', null, $user['token']);

        $this->assertApiSuccess($response, 200);
        $this->assertApiError(401, $this->api('GET', '/v1/users/me', null, $user['token']), 'token after delete:');
        $this->assertApiError(401, $this->api('POST', '/v1/auth/login', ['login' => $user['login'], 'password' => $user['password']]), 'login after delete:');
        $this->assertApiError(401, $this->api('POST', '/v1/auth/refresh', ['refresh_token' => $user['refresh']]), 'refresh after delete:');
        $this->assertApiError(401, $this->api('DELETE', '/v1/users/me', null, $user['token']), 'second delete:');
    }


    /**
     * KNOWN BUG: DELETE /users/me answers 500 "Failed to delete account" for every user with a non-public profile
     * (the default after registration): UserManageService::delete_user() looks the user up through the public
     * getter, which filters by p.is_public = 1, and throws "User not found". Expected 200
     */
    public function testDeleteMeWorksForDefaultUser(): void {
        $user = $this->register_user();

        $response = $this->api('DELETE', '/v1/users/me', null, $user['token']);

        $this->assertApiSuccess($response, 200);
    }


    public function testDeleteMeDoesNotAffectOtherUsers(): void {
        $first = $this->register_user();
        $second = $this->register_user();
        $this->assertApiSuccess($this->patch_me($first['token'], ['is_public' => 1]), 200);

        $this->assertApiSuccess($this->api('DELETE', '/v1/users/me', null, $first['token']), 200);

        $this->assertSame($second['id'], (int)$this->me($second['token'])['id']);
    }


    public function testDeletedLoginStaysReservedOrIsFree(): void {
        // documents current behaviour: the account is soft-deleted, so its login and email can not be registered again
        $user = $this->register_user();
        $this->assertApiSuccess($this->patch_me($user['token'], ['is_public' => 1]), 200);
        $this->assertApiSuccess($this->api('DELETE', '/v1/users/me', null, $user['token']), 200);

        $response = $this->api('POST', '/v1/auth/register', [
            'login' => $user['login'],
            'email' => 'again_'.bin2hex(random_bytes(4)).'@example.invalid',
            'password' => 'Test12345!',
        ]);

        $this->assertApiError(409, $response);
    }


    // ------------------------------------------------------------------ avatar


    public function testAvatarEndpointsRequireAuthentication(): void {
        $this->assertApiError(401, $this->api('POST', '/v1/users/me/avatar'));
        $this->assertApiError(401, $this->api('DELETE', '/v1/users/me/avatar'));
    }


    public function testUploadAvatarWithoutFileReturns400(): void {
        $user = $this->register_user();

        $response = $this->api('POST', '/v1/users/me/avatar', null, $user['token']);
        $this->assertApiError(400, $response, 'no body:');

        // file in a field with another name
        $response = $this->upload_avatar($user['token'], 'a.png', base64_decode(self::PNG_BASE64), 'image/png', 'file');
        $this->assertApiError(400, $response, 'wrong field name:');
    }


    public function testUploadAndDeleteAvatar(): void {
        $user = $this->register_user();
        $png = base64_decode(self::PNG_BASE64, true);
        $this->assertTrue($png !== false && str_starts_with($png, "\x89PNG"), 'test PNG must be valid');

        $response = $this->upload_avatar($user['token'], 'avatar.png', $png, 'image/png');

        $this->assertApiSuccess($response, 201, 'upload:');
        $path = $this->data($response, 'avatar_path');
        $this->assertTrue(is_string($path) && preg_match('#^av/avatar_'.$user['id'].'_\d+\.png$#', $path) === 1, 'avatar_path format, got '.json_encode($path));

        $this->assertSame($path, $this->me($user['token'])['avatar'], 'profile must reference the avatar');

        // a second upload replaces the first one
        $second = $this->upload_avatar($user['token'], 'avatar2.png', $png, 'image/png');
        $this->assertApiSuccess($second, 201, 'second upload:');
        $this->assertSame($this->data($second, 'avatar_path'), $this->me($user['token'])['avatar']);

        // delete
        $this->assertApiSuccess($this->api('DELETE', '/v1/users/me/avatar', null, $user['token']), 200, 'delete:');
        $avatar = $this->me($user['token'])['avatar'] ?? null;
        $this->assertTrue($avatar === null || $avatar === '', 'avatar must be cleared');

        // nothing left to delete
        $this->assertApiError(404, $this->api('DELETE', '/v1/users/me/avatar', null, $user['token']), 'second delete:');
    }


    public function testDeleteAvatarWithoutAvatarReturns404(): void {
        $user = $this->register_user();

        $this->assertApiError(404, $this->api('DELETE', '/v1/users/me/avatar', null, $user['token']));
    }


    public function testUploadNonImageIsRejected(): void {
        $user = $this->register_user();

        $text = $this->upload_avatar($user['token'], 'note.txt', "just text\n", 'text/plain');
        $this->assertTrue($text['status'] >= 400 && $text['status'] < 500, 'text file: got '.$text['status'].' '.substr($text['body'], 0, 200));

        $pdf = $this->upload_avatar($user['token'], 'doc.pdf', "%PDF-1.4\n%fake\n", 'application/pdf');
        $this->assertTrue($pdf['status'] >= 400 && $pdf['status'] < 500, 'pdf file: got '.$pdf['status'].' '.substr($pdf['body'], 0, 200));

        $avatar = $this->me($user['token'])['avatar'] ?? null;
        $this->assertTrue($avatar === null || $avatar === '', 'avatar must stay empty after rejected uploads');
    }


    public function testUploadScriptDisguisedAsImageIsRejected(): void {
        $user = $this->register_user();

        foreach([['shell.png', 'image/png'], ['shell.php', 'image/png'], ['shell.php.png', 'image/png'], ['shell.php', 'application/x-php']] as [$name, $mime]) {
            $response = $this->upload_avatar($user['token'], $name, '<?php echo "pwned"; ?>', $mime);
            $this->assertTrue(
                $response['status'] >= 400 && $response['status'] < 500,
                $name.' ('.$mime.') with PHP content must be rejected, got '.$response['status'].' '.substr($response['body'], 0, 200)
            );
        }

        $avatar = $this->me($user['token'])['avatar'] ?? null;
        $this->assertTrue($avatar === null || $avatar === '', 'avatar must stay empty');
    }


    public function testUploadEmptyFileIsRejected(): void {
        $user = $this->register_user();

        $response = $this->upload_avatar($user['token'], 'empty.png', '', 'image/png');

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'got '.$response['status'].' '.substr($response['body'], 0, 200));
    }
}
