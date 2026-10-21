<?php declare(strict_types=1);

/**
 * HTTP tests of moderation: POST /api/v1/moderate/ban/{id}, GET /api/v1/moderate/unban/{id}
 *
 * Tests with a moderator/admin need ROOCMS_TEST_MODERATOR_* / ROOCMS_TEST_ADMIN_* (skipped otherwise).
 * Everything that gets banned or promoted is unbanned/deleted in tearDown().
 * The shared admin and moderator accounts are only used as moderators or as targets that must be refused.
 */
require_once __DIR__.'/ApiTestCase.php';

class ModerateTest extends ApiTestCase {

    private function ban(?string $token, int $id, ?array $body = null): array {
        return $this->api('POST', '/v1/moderate/ban/'.$id, $body ?? [], $token);
    }


    private function unban(?string $token, int $id): array {
        return $this->api('GET', '/v1/moderate/unban/'.$id, null, $token);
    }


    private function login(array $user): array {
        return $this->api('POST', '/v1/auth/login', ['login' => $user['login'], 'password' => $user['password']]);
    }


    /**
     * Make sure the user is unbanned at the end of the test (registered after create_user, so it runs before user deletion)
     */
    private function unban_on_cleanup(int $id, string $role = 'moderator'): void {
        $this->on_cleanup(function() use ($id) {
            $this->unban($this->token_for($role), $id);
        });
    }


    /**
     * Created user with the given role (set through ACP by the shared admin)
     */
    private function create_user_with_role(string $role): array {
        $user = $this->create_user();
        $response = $this->api('PATCH', '/v1/acp/users/'.$user['id'].'/role', ['role' => $role], $this->token_for('admin'));
        $this->assertApiSuccess($response, 200, 'set role '.$role.':');

        return $user;
    }


    // ---------------------------------------------------------------- access control (executable without credentials)

    public function testBanAndUnbanRequireToken(): void {
        $this->assertApiError(401, $this->ban(null, 1));
        $this->assertApiError(401, $this->unban(null, 1));
    }


    public function testBanAndUnbanRejectInvalidToken(): void {
        $this->assertApiError(401, $this->ban('invalid.token.value', 1));
        $this->assertApiError(401, $this->unban('invalid.token.value', 1));
    }


    public function testRegularUserCannotBan(): void {
        $user = $this->create_user();
        $victim = $this->create_user();

        $response = $this->ban($user['token'], $victim['id'], ['reason' => 'nope']);
        $this->assertApiError(403, $response);

        $this->assertApiSuccess($this->login($victim), 200, 'victim must still be able to log in:');
    }


    public function testRegularUserCannotUnban(): void {
        $user = $this->create_user();
        $victim = $this->create_user();

        $this->assertApiError(403, $this->unban($user['token'], $victim['id']));
    }


    public function testRegularUserCannotBanThemselvesOrMissingUser(): void {
        $user = $this->create_user();

        // role check happens before any lookup: no information about the target leaks
        $this->assertApiError(403, $this->ban($user['token'], $user['id']));
        $this->assertApiError(403, $this->ban($user['token'], 2147483000));
        $this->assertApiError(403, $this->unban($user['token'], 2147483000));
    }


    // ---------------------------------------------------------------- moderator: ban / unban (blind)

    public function testModeratorBansAndUnbansUser(): void {
        $moderator = $this->token_for('moderator');
        $victim = $this->create_user();
        $this->unban_on_cleanup($victim['id']);

        $until = time() + 3600;
        $response = $this->ban($moderator, $victim['id'], ['until' => $until, 'reason' => 'test ban']);
        $this->assertApiSuccess($response);

        // banned user can neither log in nor use the existing token
        $login = $this->login($victim);
        $this->assertApiError(403, $login, 'login of banned user:');
        $this->assertApiError(403, $this->api('GET', '/v1/users/me', null, $victim['token']), 'existing token of banned user:');

        // unban restores access
        $this->assertApiSuccess($this->unban($moderator, $victim['id']));
        $this->assertApiSuccess($this->login($victim), 200, 'login after unban:');
        $this->assertApiSuccess($this->api('GET', '/v1/users/me', null, $victim['token']), 200, 'old token after unban:');
    }


    public function testBanWithoutUntilIsPermanent(): void {
        $moderator = $this->token_for('moderator');
        $victim = $this->create_user();
        $this->unban_on_cleanup($victim['id']);

        $response = $this->ban($moderator, $victim['id'], ['reason' => 'permanent']);
        $this->assertApiSuccess($response);

        // KNOWN BUG: permanent ban stores ban_expired=0 and validate_account_status() treats "ban_expired > time()" as banned, so a permanent ban does not block anything
        $this->assertApiError(403, $this->login($victim), 'login after permanent ban:');
        $this->assertApiError(403, $this->api('GET', '/v1/users/me', null, $victim['token']), 'token after permanent ban:');
    }


    public function testBanExpiredInThePastIsRejected(): void {
        $moderator = $this->token_for('moderator');
        $victim = $this->create_user();
        $this->unban_on_cleanup($victim['id']);

        $response = $this->ban($moderator, $victim['id'], ['until' => time() - 3600, 'reason' => 'past']);
        $this->assertApiError(400, $response);

        $this->assertApiSuccess($this->login($victim), 200, 'user must not be banned:');
    }


    public function testBanWithNonNumericUntilIsRejected(): void {
        $moderator = $this->token_for('moderator');
        $victim = $this->create_user();
        $this->unban_on_cleanup($victim['id']);

        $this->assertApiError(400, $this->ban($moderator, $victim['id'], ['until' => 'tomorrow']));
        $this->assertApiError(400, $this->ban($moderator, $victim['id'], ['until' => ['x']]));

        $this->assertApiSuccess($this->login($victim), 200, 'user must not be banned:');
    }


    public function testBanWithNonStringReasonIsRejected(): void {
        $moderator = $this->token_for('moderator');
        $victim = $this->create_user();
        $this->unban_on_cleanup($victim['id']);

        $this->assertApiError(400, $this->ban($moderator, $victim['id'], ['reason' => ['a', 'b']]));
        $this->assertApiError(400, $this->ban($moderator, $victim['id'], ['reason' => 12345]));
    }


    public function testBanWithTooLongReasonIsRejected(): void {
        $moderator = $this->token_for('moderator');
        $victim = $this->create_user();
        $this->unban_on_cleanup($victim['id']);

        // KNOWN BUG (suspected): controller has no length limit for the reason; a long text must be rejected with 400, not 500 or silently truncated
        $response = $this->ban($moderator, $victim['id'], ['until' => time() + 3600, 'reason' => str_repeat('x', 5000)]);
        $this->assertApiError(400, $response);
    }


    public function testBanMissingUserIsNotFound(): void {
        $moderator = $this->token_for('moderator');

        $this->assertApiError(404, $this->ban($moderator, 2147483000, ['reason' => 'ghost']));
        $this->assertApiError(404, $this->unban($moderator, 2147483000));
    }


    public function testBanReasonIsStrippedFromTags(): void {
        $moderator = $this->token_for('moderator');
        $admin = $this->token_for('admin');
        $victim = $this->create_user();
        $this->unban_on_cleanup($victim['id']);

        $this->assertApiSuccess($this->ban($moderator, $victim['id'], ['until' => time() + 3600, 'reason' => '<b>spam</b>']));

        $shown = $this->api('GET', '/v1/acp/users/'.$victim['id'], null, $admin);
        $this->assertApiSuccess($shown);
        $this->assertSame('spam', $this->data($shown, 'ban_reason'));
        $this->assertSame(1, (int)$this->data($shown, 'is_banned'));
    }


    public function testUnbanClearsBanFields(): void {
        $moderator = $this->token_for('moderator');
        $admin = $this->token_for('admin');
        $victim = $this->create_user();
        $this->unban_on_cleanup($victim['id']);

        $this->assertApiSuccess($this->ban($moderator, $victim['id'], ['until' => time() + 3600, 'reason' => 'temp']));
        $this->assertApiSuccess($this->unban($moderator, $victim['id']));

        $shown = $this->api('GET', '/v1/acp/users/'.$victim['id'], null, $admin);
        $this->assertSame(0, (int)$this->data($shown, 'is_banned'));
        $this->assertSame('', (string)$this->data($shown, 'ban_reason'));
        $this->assertSame(0, (int)$this->data($shown, 'ban_expired'));
    }


    // ---------------------------------------------------------------- role hierarchy (blind)

    public function testModeratorCannotBanOrUnbanAdmin(): void {
        $admin_id = (int)$this->session_for('admin')['user']['user_id'];
        $moderator = $this->token_for('moderator');

        $this->assertApiError(403, $this->ban($moderator, $admin_id, ['until' => time() + 3600, 'reason' => 'coup']));
        $this->assertApiError(403, $this->unban($moderator, $admin_id));
    }


    public function testNobodyCanBanThemselves(): void {
        $admin_id = (int)$this->session_for('admin')['user']['user_id'];
        $moderator_id = (int)$this->session_for('moderator')['user']['user_id'];

        $this->assertApiError(403, $this->ban($this->token_for('admin'), $admin_id, ['until' => time() + 3600]));
        $this->assertApiError(403, $this->unban($this->token_for('admin'), $admin_id));
        $this->assertApiError(403, $this->ban($this->token_for('moderator'), $moderator_id, ['until' => time() + 3600]));
        $this->assertApiError(403, $this->unban($this->token_for('moderator'), $moderator_id));
    }


    public function testModeratorCannotBanAnotherModerator(): void {
        $other = $this->create_user_with_role('m');
        $this->unban_on_cleanup($other['id'], 'admin');

        $moderator = $this->token_for('moderator');
        $this->assertApiError(403, $this->ban($moderator, $other['id'], ['until' => time() + 3600]));
        $this->assertApiError(403, $this->unban($moderator, $other['id']));

        // the target must not be banned
        $this->assertApiSuccess($this->login($other), 200, 'moderator must be able to log in:');
    }


    public function testAdminCanBanAndUnbanModerator(): void {
        $other = $this->create_user_with_role('m');
        $this->unban_on_cleanup($other['id'], 'admin');

        $admin = $this->token_for('admin');
        $this->assertApiSuccess($this->ban($admin, $other['id'], ['until' => time() + 3600, 'reason' => 'by admin']));
        $this->assertApiError(403, $this->login($other), 'banned moderator login:');

        $this->assertApiSuccess($this->unban($admin, $other['id']));
        $this->assertApiSuccess($this->login($other), 200, 'login after unban:');
    }


    public function testAdminCannotBanAnotherAdmin(): void {
        $other = $this->create_user_with_role('a');
        $this->unban_on_cleanup($other['id'], 'admin');

        $admin = $this->token_for('admin');
        $this->assertApiError(403, $this->ban($admin, $other['id'], ['until' => time() + 3600]));
        $this->assertApiSuccess($this->login($other), 200, 'admin must not be banned:');
    }


    public function testBannedModeratorCannotModerate(): void {
        $other = $this->create_user_with_role('m');
        $this->unban_on_cleanup($other['id'], 'admin');
        $victim = $this->create_user();
        $this->unban_on_cleanup($victim['id'], 'admin');

        $this->assertApiSuccess($this->ban($this->token_for('admin'), $other['id'], ['until' => time() + 3600]));

        // the token issued before the ban must not work for moderation
        $login_token = $other['token'];
        $this->assertApiError(403, $this->ban($login_token, $victim['id'], ['until' => time() + 3600]));
        $this->assertApiSuccess($this->login($victim), 200, 'victim must not be banned:');
    }
}
