<?php declare(strict_types=1);

/**
 * Backup API: /api/v1/backup/*
 *
 * Executable without credentials: 401 without token, 403 for a regular user.
 * Admin tests need ROOCMS_TEST_ADMIN_LOGIN / _PASSWORD. The only backup file they create is removed in cleanup.
 * Restore is never called with a real file: only the rejection of a missing/invalid filename is tested.
 *
 * Response shapes (from BackupService): create = data{success, data{filename, filepath, size, ...}, message};
 * list = data{success, data[], count}; status = data{success, data{backup_count, ...}}.
 * The service result is wrapped by json_response(), so the useful payload is at data.data.
 */
require_once __DIR__.'/ApiTestCase.php';

class BackupTest extends ApiTestCase {

    private const MISSING_FILE = 'apitest_nonexistent_backup_file.sql';


    // ---------------------------------------------------------------- access control (executable without credentials)

    public function testBackupEndpointsRequireToken(): void {
        foreach($this->guarded_requests() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body);

            $this->assertApiError(401, $response, $method.' '.$path.':');
        }
    }


    public function testBackupEndpointsAreForbiddenForRegularUser(): void {
        $user = $this->create_user();

        foreach($this->guarded_requests() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, $user['token']);

            $this->assertApiError(403, $response, $method.' '.$path.':');
        }
    }


    public function testBackupEndpointsAreForbiddenForModerator(): void {
        // written from the code, needs moderator credentials
        $token = $this->token_for('moderator');

        foreach($this->guarded_requests() as [$method, $path, $body]) {
            $response = $this->api($method, $path, $body, $token);

            $this->assertApiError(403, $response, $method.' '.$path.':');
        }
    }


    /**
     * Requests that must be blocked by the middleware. Bodies are harmless on purpose (invalid backup name,
     * no filename, nonexistent file) so that even a broken middleware cannot create, restore or delete anything real
     *
     * @return array<int, array{0:string, 1:string, 2:?array}>
     */
    private function guarded_requests(): array {
        return [
            ['GET',    '/v1/backup/status', null],
            ['GET',    '/v1/backup/list', null],
            ['GET',    '/v1/backup/logs', null],
            ['POST',   '/v1/backup/create', ['filename' => 'bad/name']],
            ['POST',   '/v1/backup/restore', []],
            ['GET',    '/v1/backup/download/'.self::MISSING_FILE, null],
            ['DELETE', '/v1/backup/delete/'.self::MISSING_FILE, null],
        ];
    }


    // ---------------------------------------------------------------- admin: read-only endpoints (written from the code)

    public function testAdminReadsStatus(): void {
        $response = $this->api('GET', '/v1/backup/status', null, $this->token_for('admin'));

        $this->assertApiSuccess($response);
        $status = $this->data($response, 'data');

        $this->assertTrue(is_int($status['backup_count'] ?? null) && $status['backup_count'] >= 0, 'backup_count must be a non-negative int');
        $this->assertTrue(is_int($status['total_size'] ?? null) || is_float($status['total_size'] ?? null), 'total_size must be numeric');
        $this->assertTrue(is_string($status['total_size_human'] ?? null), 'total_size_human must be a string');
        $this->assertTrue(is_bool($status['compression_enabled'] ?? null), 'compression_enabled must be bool');
        $this->assertTrue(is_bool($status['backup_directory_writable'] ?? null), 'backup_directory_writable must be bool');
    }


    public function testAdminReadsList(): void {
        $response = $this->api('GET', '/v1/backup/list', null, $this->token_for('admin'));

        $this->assertApiSuccess($response);
        $list = $this->data($response, 'data');
        $this->assertTrue(is_array($list), 'data.data must be an array');
        $this->assertSame(count($list), $this->data($response, 'count'));

        foreach($list as $backup) {
            foreach(['filename', 'size', 'size_human', 'created', 'compressed'] as $field) {
                $this->assertTrue(array_key_exists($field, $backup), 'backup entry has no '.$field);
            }
        }
    }


    public function testListDoesNotExposeServerFilesystemPaths(): void {
        // KNOWN BUG: list (and create) return "filepath", the absolute path of the backup directory on the server.
        // Clients only need the filename (download/delete are by filename)
        $response = $this->api('GET', '/v1/backup/list', null, $this->token_for('admin'));

        $this->assertApiSuccess($response);
        foreach($this->data($response, 'data') as $backup) {
            $this->assertFalse(array_key_exists('filepath', $backup), 'backup entry must not expose filepath');
        }
    }


    public function testAdminReadsLogs(): void {
        $response = $this->api('GET', '/v1/backup/logs?limit=5', null, $this->token_for('admin'));

        $this->assertApiSuccess($response);
        $this->assertTrue(is_array($this->data($response, 'data')), 'data.data must be an array');
        $this->assertSame(0, $this->data($response, 'count'));
    }


    // ---------------------------------------------------------------- admin: create / download / delete flow

    public function testAdminCreatesDownloadsAndDeletesBackup(): void {
        $token = $this->token_for('admin');
        $filename = $this->create_test_backup($token);

        // listed
        $list = $this->api('GET', '/v1/backup/list', null, $token);
        $this->assertApiSuccess($list);
        $this->assertContains($filename, array_column($this->data($list, 'data'), 'filename'));

        // downloadable
        $download = $this->http('GET', '/api/v1/backup/download/'.$filename, null, ['Authorization' => 'Bearer '.$token]);
        $this->assertStatus(200, $download, 'download:');
        $this->assertStringContains('application/octet-stream', $download['headers']['content-type'] ?? '');
        $this->assertStringContains($filename, $download['headers']['content-disposition'] ?? '');
        $this->assertTrue($download['body'] !== '', 'downloaded backup is empty');
        if(str_ends_with($filename, '.gz')) {
            $this->assertSame("\x1f\x8b", substr($download['body'], 0, 2), 'compressed backup must have gzip signature');
        }

        // delete
        $delete = $this->api('DELETE', '/v1/backup/delete/'.$filename, null, $token);
        $this->assertApiSuccess($delete, 200, 'delete:');
        $this->assertSame($filename, $this->data($delete, 'filename'));

        // gone
        $after = $this->api('GET', '/v1/backup/list', null, $token);
        $this->assertTrue(!in_array($filename, array_column($this->data($after, 'data'), 'filename'), true), 'deleted backup is still listed');
        $this->assertApiError(404, $this->http('GET', '/api/v1/backup/download/'.$filename, null, ['Authorization' => 'Bearer '.$token]), 'download after delete:');
    }


    public function testCreateBackupIsReflectedInStatus(): void {
        $token = $this->token_for('admin');

        $before = $this->data($this->api('GET', '/v1/backup/status', null, $token), 'data')['backup_count'];
        $this->create_test_backup($token);
        $after = $this->data($this->api('GET', '/v1/backup/status', null, $token), 'data')['backup_count'];

        $this->assertSame($before + 1, $after);
    }


    public function testCreateRejectsInvalidBackupName(): void {
        $token = $this->token_for('admin');

        foreach(['../evil', 'dir/evil', 'evil name', 'evil;drop'] as $name) {
            // KNOWN BUG: BackupController::create maps every exception, including InvalidArgumentException from
            // validation, to HTTP 500. Invalid client input must be a 4xx (400/422)
            $response = $this->api('POST', '/v1/backup/create', ['filename' => $name, 'include_data' => false], $token);

            $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'filename "'.$name.'" must be a client error, got '.$response['status']);
            $this->assertTrue(($response['json']['error'] ?? false) === true);
        }
    }


    public function testCreateRejectsInvalidExcludeTables(): void {
        // KNOWN BUG: same as above, InvalidArgumentException is returned as HTTP 500
        $response = $this->api('POST', '/v1/backup/create', ['exclude_tables' => 'users', 'filename' => 'apitest_'.bin2hex(random_bytes(4))], $this->token_for('admin'));

        $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'invalid exclude_tables must be a client error, got '.$response['status']);
    }


    // ---------------------------------------------------------------- admin: filenames

    public function testDeleteAndDownloadRejectForbiddenFilenames(): void {
        $token = $this->token_for('admin');
        $auth = ['Authorization' => 'Bearer '.$token];

        $names = [
            '..%2F..%2Fapitest_nonexistent.sql',  // traversal
            '..%5C..%5Capitest_nonexistent.sql',  // traversal with backslashes
            'dir%2Fapitest_nonexistent.sql',      // slash
            'apitest_nonexistent.php',            // wrong extension
            'apitest_nonexistent.txt',            // wrong extension
            'apitest%20name.sql',                 // space
            '%00.sql',                            // null byte
        ];

        foreach($names as $name) {
            $download = $this->http('GET', '/api/v1/backup/download/'.$name, null, $auth);
            $this->assertTrue($download['status'] >= 400 && $download['status'] < 500, 'download '.$name.' must be a client error, got '.$download['status']);

            // KNOWN BUG: a well-formed but missing file in delete ends as HTTP 500 instead of 404 (see below)
            $delete = $this->http('DELETE', '/api/v1/backup/delete/'.$name, null, $auth);
            $this->assertTrue($delete['status'] >= 400 && $delete['status'] < 500, 'delete '.$name.' must be a client error, got '.$delete['status']);
        }
    }


    public function testDownloadOfMissingFileIsNotFound(): void {
        $response = $this->http('GET', '/api/v1/backup/download/'.self::MISSING_FILE, null, ['Authorization' => 'Bearer '.$this->token_for('admin')]);

        $this->assertApiError(404, $response);
    }


    public function testDeleteOfMissingFileIsNotFound(): void {
        // KNOWN BUG: BackupService::delete_backup throws RuntimeException for a missing file and the controller
        // returns it as HTTP 500. Expected: 404
        $response = $this->api('DELETE', '/v1/backup/delete/'.self::MISSING_FILE, null, $this->token_for('admin'));

        $this->assertApiError(404, $response);
    }


    // ---------------------------------------------------------------- admin: restore (never with a real file)

    public function testRestoreWithoutFilenameIsRejected(): void {
        $token = $this->token_for('admin');

        $this->assertApiError(400, $this->api('POST', '/v1/backup/restore', [], $token), 'empty body:');
        $this->assertApiError(400, $this->api('POST', '/v1/backup/restore', ['filename' => ''], $token), 'empty filename:');
        $this->assertApiError(400, $this->api('POST', '/v1/backup/restore', ['drop_existing' => true], $token), 'options only:');
    }


    public function testRestoreRejectsInvalidFilename(): void {
        $token = $this->token_for('admin');

        // validation happens before the restore, so nothing can be restored with these names
        foreach(['../apitest_nonexistent.sql', 'dir/apitest_nonexistent.sql', 'apitest_nonexistent.php'] as $name) {
            // KNOWN BUG: InvalidArgumentException("Invalid backup filename") is returned as HTTP 500, expected 400
            $response = $this->api('POST', '/v1/backup/restore', ['filename' => $name], $token);

            $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'restore with "'.$name.'" must be a client error, got '.$response['status']);
        }
    }


    // ---------------------------------------------------------------- helpers

    /**
     * Create a small structure-only backup with a unique name and register its removal. Returns the filename
     */
    private function create_test_backup(string $token): string {
        $name = 'apitest_'.bin2hex(random_bytes(4));

        // the file name is known in advance: schedule removal before the call so a failed test leaves nothing behind
        foreach(['.sql.gz', '.sql'] as $extension) {
            $this->on_cleanup(function() use ($name, $extension, $token) {
                $this->api('DELETE', '/v1/backup/delete/'.$name.$extension, null, $token);
            });
        }

        $response = $this->api('POST', '/v1/backup/create', [
            'filename'          => $name,
            'compress'          => true,
            'include_data'      => false,
            'include_structure' => true,
        ], $token);

        $this->assertApiSuccess($response, 200, 'create backup:');

        $result = $this->data($response, 'data');
        $filename = $result['filename'] ?? null;
        $this->assertTrue(is_string($filename) && str_starts_with($filename, $name), 'create must return the filename');
        $this->assertTrue(($result['size'] ?? 0) > 0, 'backup size must be positive');

        return $filename;
    }
}
