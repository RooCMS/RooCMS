<?php declare(strict_types=1);

/**
 * HTTP tests of the admin user management: /api/v1/acp/users
 *
 * Access-control tests run without credentials (401, 403 for a regular user).
 * Tests with an admin need ROOCMS_TEST_ADMIN_* (skipped otherwise); they work only with users created by the test.
 * The shared admin and moderator accounts are never modified or deleted.
 */
require_once __DIR__.'/ApiTestCase.php';

class AcpUsersTest extends ApiTestCase {

    /**
     * All routes of the controller: [method, path template, body]
     *
     * @return array<int, array{0:string,1:string,2:?array}>
     */
    private function routes(int $id): array {
        $base = '/v1/acp/users';

        return [
            ['GET',    $base.'/roles', null],
            ['GET',    $base.'/'.$id, null],
            ['PUT',    $base.'/'.$id, ['nickname' => 'hacked']],
            ['PUT',    $base.'/'.$id.'/password', ['password' => 'Hacked12345!']],
            ['PATCH',  $base.'/'.$id.'/role', ['role' => 'a']],
            ['PATCH',  $base.'/'.$id.'/activate', []],
            ['PATCH',  $base.'/'.$id.'/deactivate', []],
            ['PATCH',  $base.'/'.$id.'/verify', []],
            ['PATCH',  $base.'/'.$id.'/unverify', []],
            ['DELETE', $base.'/'.$id.'/avatar', null],
            ['DELETE', $base.'/'.$id, null],
        ];
    }


    private function login(array $user, ?string $password = null): array {
        return $this->api('POST', '/v1/auth/login', ['login' => $user['login'], 'password' => $password ?? $user['password']]);
    }


    private function acp(string $method, string $path, ?array $json = null, ?string $token = null): array {
        return $this->api($method, '/v1/acp/users'.$path, $json, $token ?? $this->token_for('admin'));
    }


    /**
     * Created user with the given role (set through ACP by the shared admin)
     */
    private function create_user_with_role(string $role): array {
        $user = $this->create_user();
        $this->assertApiSuccess($this->acp('PATCH', '/'.$user['id'].'/role', ['role' => $role]), 200, 'set role '.$role.':');

        return $user;
    }


    /**
     * Cleanup that re-activates the user so that create_user() cleanup can delete the account
     */
    private function activate_on_cleanup(int $id): void {
        $this->on_cleanup(function() use ($id) {
            getenv('ROOCMS_TEST_ADMIN_LOGIN') && $this->acp('PATCH', '/'.$id.'/activate');
        });
    }


    // ---------------------------------------------------------------- access control (executable without credentials)

    public function testAllRoutesRequireToken(): void {
        foreach($this->routes(1) as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body);
            $this->assertApiError(401, $response, $method.' '.$path.':');
        }
    }


    public function testAllRoutesRejectInvalidToken(): void {
        foreach($this->routes(1) as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, 'invalid.token.value');
            $this->assertApiError(401, $response, $method.' '.$path.':');
        }
    }


    public function testAllRoutesForbiddenForRegularUser(): void {
        $user = $this->create_user();
        $target = $this->create_user();

        foreach($this->routes((int)$target['id']) as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, $user['token']);
            $this->assertApiError(403, $response, $method.' '.$path.':');
        }

        // nothing was changed or deleted
        $this->assertApiSuccess($this->login($target), 200, 'target must be intact:');
        $this->assertApiError(401, $this->login($target, 'Hacked12345!'), 'password must not change:');
    }


    public function testRegularUserCannotPromoteThemselves(): void {
        $user = $this->create_user();

        $this->assertApiError(403, $this->api('PATCH', '/v1/acp/users/'.$user['id'].'/role', ['role' => 'su'], $user['token']));
        $this->assertApiError(403, $this->api('PUT', '/v1/acp/users/'.$user['id'], ['role' => 'su'], $user['token']));

        $me = $this->api('GET', '/v1/users/me', null, $user['token']);
        $this->assertApiSuccess($me);
        $this->assertSame('u', $this->data($me, 'role'));
    }


    public function testAllRoutesForbiddenForModerator(): void {
        $target = $this->create_user();
        $moderator = $this->token_for('moderator');

        foreach($this->routes((int)$target['id']) as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, $moderator);
            $this->assertApiError(403, $response, $method.' '.$path.':');
        }
    }


    // ---------------------------------------------------------------- roles / show (blind)

    public function testRolesList(): void {
        $response = $this->acp('GET', '/roles');
        $this->assertApiSuccess($response);

        $roles = $this->data($response);
        foreach(['u' => 1, 'm' => 2, 'a' => 3, 'su' => 4] as $key => $level) {
            $this->assertTrue(isset($roles[$key]), 'role '.$key.' must be listed');
            $this->assertSame($key, $roles[$key]['key']);
            $this->assertSame($level, (int)$roles[$key]['level']);
            $this->assertTrue(!empty($roles[$key]['name']) && !empty($roles[$key]['description']));
        }
    }


    public function testShowCreatedUser(): void {
        $user = $this->create_user();

        $response = $this->acp('GET', '/'.$user['id']);
        $this->assertApiSuccess($response);
        $this->assertSame((int)$user['id'], (int)$this->data($response, 'id'));
        $this->assertSame($user['login'], $this->data($response, 'login'));
        $this->assertSame($user['email'], $this->data($response, 'email'));
        $this->assertSame('u', $this->data($response, 'role'));
        $this->assertTrue(!array_key_exists('password', $response['json']['data']), 'password hash must not be exposed');
    }


    public function testShowMissingUserIsNotFound(): void {
        $this->assertApiError(404, $this->acp('GET', '/2147483000'));
    }


    // ---------------------------------------------------------------- update (blind)

    public function testUpdateProfileAndEmail(): void {
        $user = $this->create_user();
        $nick = 'nick'.bin2hex(random_bytes(3));
        $email = 'new_'.bin2hex(random_bytes(3)).'@example.invalid';

        $response = $this->acp('PUT', '/'.$user['id'], ['email' => $email, 'nickname' => $nick, 'first_name' => 'Test']);
        $this->assertApiSuccess($response);

        $shown = $this->acp('GET', '/'.$user['id']);
        $this->assertSame($email, $this->data($shown, 'email'));
        $this->assertSame($nick, $this->data($shown, 'nickname'));
        $this->assertSame('Test', $this->data($shown, 'first_name'));
    }


    public function testUpdateWithoutDataIsBadRequest(): void {
        $user = $this->create_user();

        $this->assertApiError(400, $this->acp('PUT', '/'.$user['id'], []));
    }


    public function testUpdateInvalidEmailIsRejected(): void {
        $user = $this->create_user();

        $this->assertApiError(422, $this->acp('PUT', '/'.$user['id'], ['email' => 'not-an-email']));

        $shown = $this->acp('GET', '/'.$user['id']);
        $this->assertSame($user['email'], $this->data($shown, 'email'));
    }


    public function testUpdateDuplicateEmailIsConflict(): void {
        $first = $this->create_user();
        $second = $this->create_user();

        $this->assertApiError(409, $this->acp('PUT', '/'.$second['id'], ['email' => $first['email']]));
    }


    public function testUpdateOwnEmailIsAllowed(): void {
        $user = $this->create_user();

        $this->assertApiSuccess($this->acp('PUT', '/'.$user['id'], ['email' => $user['email']]));
    }


    public function testUpdateDuplicateNicknameIsConflict(): void {
        $first = $this->create_user();
        $second = $this->create_user();
        $nick = 'dup'.bin2hex(random_bytes(3));

        $this->assertApiSuccess($this->acp('PUT', '/'.$first['id'], ['nickname' => $nick]));
        $this->assertApiError(409, $this->acp('PUT', '/'.$second['id'], ['nickname' => $nick]));
    }


    public function testUpdateInvalidRoleIsRejected(): void {
        $user = $this->create_user();

        $this->assertApiError(422, $this->acp('PUT', '/'.$user['id'], ['role' => 'root']));

        $this->assertSame('u', $this->data($this->acp('GET', '/'.$user['id']), 'role'));
    }


    public function testUpdateInvalidProfileValuesAreRejected(): void {
        $user = $this->create_user();

        $this->assertApiError(422, $this->acp('PUT', '/'.$user['id'], ['gender' => 'robot']), 'gender:');
        $this->assertApiError(422, $this->acp('PUT', '/'.$user['id'], ['website' => 'javascript:alert(1)']), 'website:');
        $this->assertApiError(422, $this->acp('PUT', '/'.$user['id'], ['birthday' => 'yesterday-ish']), 'birthday:');
        $this->assertApiError(422, $this->acp('PUT', '/'.$user['id'], ['ban_expired' => -5]), 'ban_expired:');
    }


    public function testUpdateMissingUserIsNotFound(): void {
        $this->assertApiError(404, $this->acp('PUT', '/2147483000', ['nickname' => 'ghost']));
    }


    public function testUpdateStripsTags(): void {
        $user = $this->create_user();

        $this->assertApiSuccess($this->acp('PUT', '/'.$user['id'], ['first_name' => '<script>alert(1)</script>Name']));
        $this->assertSame('alert(1)Name', $this->data($this->acp('GET', '/'.$user['id']), 'first_name'));
    }


    // ---------------------------------------------------------------- password (blind)

    public function testChangePassword(): void {
        $user = $this->create_user();
        $new = 'NewPass98765!';

        $this->assertApiSuccess($this->acp('PUT', '/'.$user['id'].'/password', ['password' => $new]));

        $this->assertApiSuccess($this->login($user, $new), 200, 'login with new password:');
        $this->assertApiError(401, $this->login($user), 'login with old password:');

        // keep create_user() cleanup working: it logs in with the original password and falls back to the old token
        $this->acp('PUT', '/'.$user['id'].'/password', ['password' => $user['password']]);
    }


    public function testChangePasswordValidation(): void {
        $user = $this->create_user();

        $this->assertApiError(400, $this->acp('PUT', '/'.$user['id'].'/password', []), 'missing field:');
        $this->assertApiError(400, $this->acp('PUT', '/'.$user['id'].'/password', ['password' => 'abc']), 'too short:');
        $this->assertApiError(400, $this->acp('PUT', '/'.$user['id'].'/password', ['password' => '']), 'empty:');

        $this->assertApiSuccess($this->login($user), 200, 'password must stay the same:');
    }


    public function testChangePasswordMissingUserIsNotFound(): void {
        $this->assertApiError(404, $this->acp('PUT', '/2147483000/password', ['password' => 'Valid12345!']));
    }


    // ---------------------------------------------------------------- role (blind)

    public function testChangeRoleAndBack(): void {
        $user = $this->create_user();

        $this->assertApiSuccess($this->acp('PATCH', '/'.$user['id'].'/role', ['role' => 'm']));
        $this->assertSame('m', $this->data($this->acp('GET', '/'.$user['id']), 'role'));

        // new role is effective for the existing token
        $this->assertApiError(404, $this->api('GET', '/v1/moderate/unban/2147483000', null, $user['token']), 'moderator access:');

        $this->assertApiSuccess($this->acp('PATCH', '/'.$user['id'].'/role', ['role' => 'u']));
        $this->assertSame('u', $this->data($this->acp('GET', '/'.$user['id']), 'role'));
        $this->assertApiError(403, $this->api('GET', '/v1/moderate/unban/2147483000', null, $user['token']), 'moderator access removed:');
    }


    public function testChangeRoleValidation(): void {
        $user = $this->create_user();

        $this->assertApiError(400, $this->acp('PATCH', '/'.$user['id'].'/role', []), 'missing field:');
        $this->assertApiError(422, $this->acp('PATCH', '/'.$user['id'].'/role', ['role' => 'root']), 'invalid role:');
        $this->assertApiError(422, $this->acp('PATCH', '/'.$user['id'].'/role', ['role' => 'A']), 'case matters:');
        $this->assertApiError(404, $this->acp('PATCH', '/2147483000/role', ['role' => 'u']), 'missing user:');

        $this->assertSame('u', $this->data($this->acp('GET', '/'.$user['id']), 'role'));
    }


    // ---------------------------------------------------------------- activate / deactivate (blind)

    public function testDeactivatedUserCannotAuthenticateAndActivateRestoresAccess(): void {
        $user = $this->create_user();
        $this->activate_on_cleanup($user['id']);

        $this->assertApiSuccess($this->acp('PATCH', '/'.$user['id'].'/deactivate'));
        $this->assertSame(0, (int)$this->data($this->acp('GET', '/'.$user['id']), 'is_active'));

        $this->assertApiError(403, $this->login($user), 'login of deactivated user:');
        // AuthenticationService::verify_token() looks only for active users, so the token is simply invalid: 401
        $this->assertApiError(401, $this->api('GET', '/v1/users/me', null, $user['token']), 'token of deactivated user:');

        $this->assertApiSuccess($this->acp('PATCH', '/'.$user['id'].'/activate'));
        $this->assertSame(1, (int)$this->data($this->acp('GET', '/'.$user['id']), 'is_active'));
        $this->assertApiSuccess($this->login($user), 200, 'login after activation:');
    }


    public function testActivateAndDeactivateMissingUserAreNotFound(): void {
        // KNOWN BUG: update_user() does not check affected rows, activate/deactivate answer 200 for a non-existing user
        $this->assertApiError(404, $this->acp('PATCH', '/2147483000/activate'));
        $this->assertApiError(404, $this->acp('PATCH', '/2147483000/deactivate'));
    }


    // ---------------------------------------------------------------- verify / unverify (blind)

    public function testVerifyAndUnverify(): void {
        $user = $this->create_user();

        $this->assertApiSuccess($this->acp('PATCH', '/'.$user['id'].'/verify'));
        $this->assertSame(1, (int)$this->data($this->acp('GET', '/'.$user['id']), 'is_verified'));

        $this->assertApiSuccess($this->acp('PATCH', '/'.$user['id'].'/unverify'));
        $this->assertSame(0, (int)$this->data($this->acp('GET', '/'.$user['id']), 'is_verified'));
    }


    public function testVerifyAndUnverifyMissingUserAreNotFound(): void {
        // KNOWN BUG: update_user() does not check affected rows, verify/unverify answer 200 for a non-existing user
        $this->assertApiError(404, $this->acp('PATCH', '/2147483000/verify'));
        $this->assertApiError(404, $this->acp('PATCH', '/2147483000/unverify'));
    }


    // ---------------------------------------------------------------- avatar (blind)

    public function testDeleteAvatarOfUserWithoutAvatarIsNotFound(): void {
        $user = $this->create_user();

        $this->assertApiError(404, $this->acp('DELETE', '/'.$user['id'].'/avatar'));
        $this->assertApiError(404, $this->acp('DELETE', '/2147483000/avatar'));
    }


    // ---------------------------------------------------------------- delete (blind)

    public function testDeleteUserIsSoftDeleteAndBlocksLogin(): void {
        $user = $this->create_user();

        $this->assertApiSuccess($this->acp('DELETE', '/'.$user['id']));

        $this->assertApiError(401, $this->login($user), 'login of deleted user:');
        $this->assertApiError(401, $this->api('GET', '/v1/users/me', null, $user['token']), 'token of deleted user:');

        // soft delete: the record is still visible to the admin and flagged
        $shown = $this->acp('GET', '/'.$user['id']);
        if($shown['status'] === 200) {
            $this->assertSame(1, (int)$this->data($shown, 'is_deleted'));
        }

        $this->assertApiError(404, $this->acp('DELETE', '/'.$user['id']), 'second delete:');
    }


    public function testDeleteMissingUserIsNotFound(): void {
        $this->assertApiError(404, $this->acp('DELETE', '/2147483000'));
    }


    // ---------------------------------------------------------------- safety rules (blind, KNOWN BUG candidates)
    // Only users created by the test are touched. UserManageService has no checks of the acting admin's identity or role level.

    public function testAdminCannotDeleteOwnAccount(): void {
        $admin = $this->create_user_with_role('a');

        // KNOWN BUG: no self-protection in UserManageService::delete_user()
        $response = $this->acp('DELETE', '/'.$admin['id'], null, $admin['token']);
        $this->assertApiError(403, $response);
    }


    public function testAdminCannotDemoteThemselves(): void {
        $admin = $this->create_user_with_role('a');

        // KNOWN BUG: no self-protection in UserManageService::change_user_role() (an admin can lock the panel out of the last superuser)
        $response = $this->acp('PATCH', '/'.$admin['id'].'/role', ['role' => 'u'], $admin['token']);
        $this->assertApiError(403, $response);
        $this->assertSame('a', $this->data($this->acp('GET', '/'.$admin['id']), 'role'));
    }


    public function testAdminCannotDeactivateThemselves(): void {
        $admin = $this->create_user_with_role('a');
        $this->activate_on_cleanup($admin['id']);

        // KNOWN BUG: no self-protection in UserManageService::deactivate_user()
        $response = $this->acp('PATCH', '/'.$admin['id'].'/deactivate', null, $admin['token']);
        $this->assertApiError(403, $response);
    }


    public function testAdminCannotGrantSuperuserRole(): void {
        $user = $this->create_user();

        // KNOWN BUG: any admin may create a superuser (privilege escalation); only a superuser should grant "su"
        $response = $this->acp('PATCH', '/'.$user['id'].'/role', ['role' => 'su']);
        $this->assertApiError(403, $response);
        $this->assertSame('u', $this->data($this->acp('GET', '/'.$user['id']), 'role'));
    }


    public function testAdminCannotGrantSuperuserRoleThroughUpdate(): void {
        $user = $this->create_user();

        // KNOWN BUG: PUT /acp/users/{id} accepts role "su" as well
        $response = $this->acp('PUT', '/'.$user['id'], ['role' => 'su']);
        $this->assertApiError(403, $response);
        $this->assertSame('u', $this->data($this->acp('GET', '/'.$user['id']), 'role'));
    }


    public function testAdminCannotChangePasswordOfAnotherAdmin(): void {
        $other = $this->create_user_with_role('a');

        // KNOWN BUG: no role hierarchy check (compare ModerateController::can_moderate): an admin can take over equal-level accounts
        $response = $this->acp('PUT', '/'.$other['id'].'/password', ['password' => 'Takeover12345!']);
        $this->assertApiError(403, $response);
        $this->assertApiSuccess($this->login($other), 200, 'password of the other admin must be unchanged:');
    }
}
