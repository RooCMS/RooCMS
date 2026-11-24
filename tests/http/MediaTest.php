<?php declare(strict_types=1);

/**
 * HTTP tests of the media API: /api/v1/media
 *
 * Every uploaded item is deleted in tearDown() through its owner token.
 */
require_once __DIR__.'/ApiTestCase.php';

class MediaTest extends ApiTestCase {

    /** 1x1 transparent PNG */
    private const PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';


    private function png(): string {
        return base64_decode(self::PNG_BASE64);
    }


    /**
     * Raw multipart/form-data upload
     *
     * @param array<string,string> $fields Plain form fields
     * @param string|null $content File content, null = no file part
     */
    private function upload(?string $token, ?string $content, string $name = 'pixel.png', string $mime = 'image/png', array $fields = [], string $field_name = 'file'): array {
        $boundary = '----RooCMSTest'.bin2hex(random_bytes(8));
        $body = '';

        foreach($fields as $key => $value) {
            $body .= "--{$boundary}\r\nContent-Disposition: form-data; name=\"{$key}\"\r\n\r\n{$value}\r\n";
        }

        if($content !== null) {
            $body .= "--{$boundary}\r\nContent-Disposition: form-data; name=\"{$field_name}\"; filename=\"{$name}\"\r\nContent-Type: {$mime}\r\n\r\n{$content}\r\n";
        }

        $body .= "--{$boundary}--\r\n";

        $headers = ['Content-Type' => 'multipart/form-data; boundary='.$boundary];
        if($token !== null) {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $this->http('POST', '/api/v1/media/upload', $body, $headers);
    }


    /**
     * Upload a PNG for the user and register deletion of the media in cleanup
     */
    private function upload_png(array $user, array $fields = [], string $name = 'pixel.png'): array {
        $response = $this->upload($user['token'], $this->png(), $name, 'image/png', $fields);
        $this->assertApiSuccess($response, 201, 'upload png:');

        $media = $this->data($response, 'media');
        $this->assertTrue(is_array($media) && isset($media['id']), 'upload must return data.media.id');

        $this->register_media_cleanup((int)$media['id'], $user['token']);

        return $media;
    }


    private function register_media_cleanup(int $id, string $token): void {
        $this->on_cleanup(function() use ($id, $token) {
            $this->api('DELETE', '/v1/media/'.$id, null, $token);
        });
    }


    // ---------------------------------------------------------------- upload

    public function testUploadPngReturnsMedia(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user, ['description' => 'tiny pixel']);

        $this->assertSame('image', $media['media_type']);
        $this->assertSame('image/png', $media['mime_type']);
        $this->assertSame('png', $media['extension']);
        $this->assertSame('pixel.png', $media['original_name']);
        $this->assertSame(strlen($this->png()), (int)$media['file_size']);
        $this->assertSame((int)$user['id'], (int)$media['user_id']);
        $this->assertSame(1, (int)$media['width']);
        $this->assertSame(1, (int)$media['height']);
        $this->assertSame('ready', $media['status']);
        $this->assertTrue(!empty($media['uuid']), 'uuid must be set');
    }


    public function testUploadRequiresAuthentication(): void {
        $response = $this->upload(null, $this->png());
        $this->assertApiError(401, $response);

        $response = $this->upload('invalid.token.value', $this->png());
        $this->assertApiError(401, $response);
    }


    public function testUploadWithoutFileIsBadRequest(): void {
        $user = $this->create_user();

        $response = $this->upload($user['token'], null, 'x', 'x', ['description' => 'no file here']);
        $this->assertApiError(400, $response);

        $response = $this->upload($user['token'], $this->png(), 'pixel.png', 'image/png', [], 'wrong_field');
        $this->assertApiError(400, $response, 'wrong field name:');
    }


    public function testUploadEmptyFileIsRejected(): void {
        $user = $this->create_user();

        $response = $this->upload($user['token'], '', 'empty.png');
        $this->assertApiError(400, $response);
    }


    public function testUploadPhpCodeWithImageExtensionIsRejected(): void {
        $user = $this->create_user();

        $response = $this->upload($user['token'], "<?php echo 'x'; ?>", 'fake.png', 'image/png');
        if($response['status'] === 201) {
            $this->register_media_cleanup((int)$this->data($response, 'media')['id'], $user['token']);
        }

        $this->assertApiError(415, $response, 'PHP source declared as image must be rejected by real MIME:');
    }


    public function testUploadPhpFileIsRejected(): void {
        $user = $this->create_user();

        $response = $this->upload($user['token'], "<?php echo 'x'; ?>", 'shell.php', 'application/x-php');
        if($response['status'] === 201) {
            $this->register_media_cleanup((int)$this->data($response, 'media')['id'], $user['token']);
        }

        $this->assertApiError(415, $response);
    }


    public function testUploadImagePolyglotWithPhpExtensionIsNotStoredAsPhp(): void {
        $user = $this->create_user();

        // valid GIF header + PHP code, named .php: must never be stored as an executable .php file
        $response = $this->upload($user['token'], "GIF89a;<?php echo 'x'; ?>", 'shell.php', 'image/gif');
        if($response['status'] === 201) {
            $media = $this->data($response, 'media');
            $this->register_media_cleanup((int)$media['id'], $user['token']);

            // KNOWN BUG: extension comes from the client file name, so a GIF polyglot is stored as <uuid>.php
            $this->assertTrue(strtolower((string)$media['extension']) !== 'php', 'stored extension must not be php, got '.$media['extension']);
            $this->assertTrue(!str_ends_with(strtolower((string)$media['filename']), '.php'), 'stored filename must not end with .php, got '.$media['filename']);
        } else {
            $this->assertTrue($response['status'] >= 400 && $response['status'] < 500, 'must be rejected with 4xx, got '.$response['status']);
        }
    }


    public function testUploadDocumentIsClassifiedAsDocument(): void {
        $user = $this->create_user();

        $response = $this->upload($user['token'], "hello media\n", 'note.txt', 'text/plain');
        $this->assertApiSuccess($response, 201);
        $media = $this->data($response, 'media');
        $this->register_media_cleanup((int)$media['id'], $user['token']);

        $this->assertSame('document', $media['media_type']);
        $this->assertSame('text/plain', $media['mime_type']);
    }


    // ---------------------------------------------------------------- show / file

    public function testShowReturnsUploadedMedia(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        $response = $this->api('GET', '/v1/media/'.$media['id']);
        $this->assertApiSuccess($response);
        $this->assertSame((int)$media['id'], (int)$this->data($response, 'id'));
        $this->assertSame($media['uuid'], $this->data($response, 'uuid'));
        $this->assertSame('pixel.png', $this->data($response, 'original_name'));
        $this->assertTrue(is_array($this->data($response, 'variants')), 'variants must be a list');
    }


    public function testShowHidesPrivateFieldsFromGuest(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        $response = $this->api('GET', '/v1/media/'.$media['id']);
        $this->assertApiSuccess($response);
        $data = $this->data($response);
        $this->assertTrue(!array_key_exists('user_id', $data), 'guest must not see user_id');
        $this->assertTrue(!array_key_exists('metadata', $data), 'guest must not see metadata');
        $this->assertTrue(!empty($data['file_path']), 'file_path must stay visible');
        $this->assertTrue(!empty($data['filename']), 'filename must stay visible');
    }


    public function testShowExposesUserIdToOwner(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        $response = $this->api('GET', '/v1/media/'.$media['id'], null, $user['token']);
        $this->assertApiSuccess($response);
        $this->assertSame((int)$user['id'], (int)$this->data($response, 'user_id'));
    }


    public function testShowHidesUserIdFromOtherUser(): void {
        $owner = $this->create_user();
        $stranger = $this->create_user();
        $media = $this->upload_png($owner);

        $response = $this->api('GET', '/v1/media/'.$media['id'], null, $stranger['token']);
        $this->assertApiSuccess($response);
        $this->assertTrue(!array_key_exists('user_id', $this->data($response)), 'other user must not see user_id');
    }


    public function testListHidesPrivateFieldsFromGuest(): void {
        $user = $this->create_user();
        $this->upload_png($user);

        $response = $this->api('GET', '/v1/media?limit=50');
        $this->assertApiSuccess($response);
        $items = $this->data($response)['data'];
        $this->assertTrue(count($items) >= 1, 'list must not be empty');
        foreach($items as $item) {
            $this->assertTrue(!array_key_exists('user_id', $item), 'guest list must not contain user_id');
            $this->assertTrue(!array_key_exists('metadata', $item), 'guest list must not contain metadata');
            $this->assertSame('ready', $item['status']);
        }
    }


    public function testListIgnoresUserIdFilterForGuest(): void {
        $user_a = $this->create_user();
        $user_b = $this->create_user();
        $this->upload_png($user_a);
        $this->upload_png($user_b);

        $response = $this->api('GET', '/v1/media?user_id='.$user_a['id']);
        $this->assertApiSuccess($response);
        $this->assertTrue(!array_key_exists('user_id', $this->data($response)['filters'] ?? []), 'user_id filter must not be applied for guest');
        $this->assertTrue((int)$this->data($response)['pagination']['total'] >= 2, 'guest must not be able to filter by uploader');
    }


    public function testShowMissingMediaIsNotFound(): void {
        $this->assertApiError(404, $this->api('GET', '/v1/media/2147483000'));
    }


    public function testDownloadReturnsUploadedBytes(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        $response = $this->api('GET', '/v1/media/'.$media['id'].'/file');
        $this->assertStatus(200, $response);
        $this->assertSame($this->png(), $response['body'], 'downloaded body must equal uploaded bytes');
        $this->assertStringContains('image/png', $response['headers']['content-type'] ?? '');
        $this->assertSame((string)strlen($this->png()), $response['headers']['content-length'] ?? '');
        $this->assertStringContains('pixel.png', $response['headers']['content-disposition'] ?? '');
    }


    public function testDownloadContentDispositionIsSafe(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user, [], "q\"uote файл.png");

        $response = $this->api('GET', '/v1/media/'.$media['id'].'/file');
        $this->assertStatus(200, $response);
        $cd = $response['headers']['content-disposition'] ?? '';
        $this->assertTrue(preg_match('/^attachment; filename="[\x20-\x7E]*"; filename\*=UTF-8\'\'[A-Za-z0-9%._~-]+$/', $cd) === 1, 'unexpected Content-Disposition: '.$cd);
        $this->assertTrue(preg_match('/filename="[^"\\\\]*"/', $cd) === 1, 'ascii filename must not contain quotes or backslashes: '.$cd);
    }


    public function testDownloadMissingMediaIsNotFound(): void {
        $this->assertApiError(404, $this->api('GET', '/v1/media/2147483000/file'));
    }


    public function testDownloadUnknownVariantFallsBackToOriginalFile(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        $response = $this->api('GET', '/v1/media/'.$media['id'].'/file?variant=nonexistent');
        $this->assertStatus(200, $response);
        $this->assertSame($this->png(), $response['body']);
    }


    // ---------------------------------------------------------------- list

    public function testListShapeAndPagination(): void {
        $user = $this->create_user();
        $this->upload_png($user);

        $response = $this->api('GET', '/v1/media?limit=10&page=1');
        $this->assertApiSuccess($response);

        $data = $this->data($response);
        $this->assertTrue(is_array($data['data'] ?? null), 'data.data must be a list');
        $this->assertTrue(count($data['data']) <= 10, 'limit must be respected');

        $pagination = $data['pagination'] ?? [];
        $this->assertSame(1, $pagination['current_page'] ?? null);
        $this->assertSame(10, $pagination['per_page'] ?? null);
        $this->assertTrue(($pagination['total'] ?? 0) >= 1);
        $this->assertSame(false, $pagination['has_prev'] ?? null);
    }


    public function testListLimitIsClamped(): void {
        $response = $this->api('GET', '/v1/media?limit=100000&page=-5');
        $this->assertApiSuccess($response);

        $pagination = $this->data($response)['pagination'];
        $this->assertSame(100, $pagination['per_page']);
        $this->assertSame(1, $pagination['current_page']);
    }


    public function testListFilterByUserShowsOnlyThatUsersMedia(): void {
        $user_a = $this->create_user();
        $user_b = $this->create_user();
        $media_a = $this->upload_png($user_a);
        $media_b = $this->upload_png($user_b);

        // user_id is exposed and filterable only for privileged viewers (moderator/admin/superuser)
        $response = $this->api('GET', '/v1/media?user_id='.$user_a['id'], null, $this->token_for('moderator'));
        $this->assertApiSuccess($response);

        $ids = array_map(fn($m) => (int)$m['id'], $this->data($response)['data']);
        $this->assertContains((int)$media_a['id'], $ids);
        $this->assertTrue(!in_array((int)$media_b['id'], $ids, true), 'other user media must not be listed');

        foreach($this->data($response)['data'] as $item) {
            $this->assertSame((int)$user_a['id'], (int)$item['user_id']);
        }
        $this->assertSame(1, (int)$this->data($response)['pagination']['total']);
    }


    public function testListFilterByTypeAndSearch(): void {
        $user = $this->create_user();
        $name = 'srch'.bin2hex(random_bytes(4)).'.png';
        $media = $this->upload_png($user, [], $name);

        // KNOWN BUG: search and update use media.description, but the column does not exist (SQL 42S22 -> 500)
        $response = $this->api('GET', '/v1/media?type=image&user_id='.$user['id'].'&search='.substr($name, 0, 12));
        $this->assertApiSuccess($response);
        $ids = array_map(fn($m) => (int)$m['id'], $this->data($response)['data']);
        $this->assertContains((int)$media['id'], $ids);

        $response = $this->api('GET', '/v1/media?type=document&user_id='.$user['id']);
        $this->assertApiSuccess($response);
        $this->assertSame(0, (int)$this->data($response)['pagination']['total']);

        $response = $this->api('GET', '/v1/media?user_id='.$user['id'].'&search=no_such_name_'.bin2hex(random_bytes(4)));
        $this->assertSame(0, (int)$this->data($response)['pagination']['total']);
    }


    public function testListInvalidTypeIsBadRequest(): void {
        $this->assertApiError(400, $this->api('GET', '/v1/media?type=bogus'));
    }


    public function testListInvalidStatusIsBadRequest(): void {
        // KNOWN BUG: Files::STATUS_TYPES is private -> Error, global handler answers HTTP 200 with internal_error
        $this->assertApiError(400, $this->api('GET', '/v1/media?status=bogus'));
    }


    public function testListFilterByValidStatus(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        // KNOWN BUG: MediaController uses Files::STATUS_TYPES, which is a private constant (Error -> 500)
        $response = $this->api('GET', '/v1/media?status=ready&user_id='.$user['id']);
        $this->assertApiSuccess($response);

        $ids = array_map(fn($m) => (int)$m['id'], $this->data($response)['data']);
        $this->assertContains((int)$media['id'], $ids);
    }


    // ---------------------------------------------------------------- update

    public function testUpdateDescriptionByOwner(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        // KNOWN BUG: UPDATE media SET description -> column does not exist (500)
        $response = $this->api('PUT', '/v1/media/'.$media['id'], ['description' => 'new description'], $user['token']);
        $this->assertApiSuccess($response);
        $this->assertSame('new description', $this->data($response)['media']['description']);

        $shown = $this->api('GET', '/v1/media/'.$media['id']);
        $this->assertSame('new description', $this->data($shown, 'description'));
    }


    public function testUpdateKeywordsByOwner(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        // KNOWN BUG: keywords column is not in the allowed fields of update_media_metadata (only "tags", which is not a media column)
        $response = $this->api('PUT', '/v1/media/'.$media['id'], ['keywords' => 'one, two'], $user['token']);
        $this->assertApiSuccess($response);
        $this->assertSame('one, two', $this->data($response)['media']['keywords']);
    }


    public function testUpdateRenameStripsTags(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        // KNOWN BUG: UPDATE media SET description -> column does not exist (500)
        $response = $this->api('PUT', '/v1/media/'.$media['id'], ['original_name' => 'renamed.png', 'description' => '<b>bold</b>'], $user['token']);
        $this->assertApiSuccess($response);
        $this->assertSame('renamed.png', $this->data($response)['media']['original_name']);
        $this->assertSame('bold', $this->data($response)['media']['description']);
    }


    public function testUpdateWithoutValidFieldsIsBadRequest(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        $response = $this->api('PUT', '/v1/media/'.$media['id'], ['unknown_field' => 'x'], $user['token']);
        $this->assertApiError(400, $response);
    }


    public function testUpdateInvalidStatusIsBadRequest(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        // KNOWN BUG: Files::STATUS_TYPES is private, referencing it from the service throws Error (HTTP 200 internal_error)
        $response = $this->api('PUT', '/v1/media/'.$media['id'], ['status' => 'bogus'], $user['token']);
        $this->assertApiError(400, $response);
    }


    public function testUpdateRequiresAuthentication(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        $response = $this->api('PUT', '/v1/media/'.$media['id'], ['description' => 'hack']);
        $this->assertApiError(401, $response);

        $shown = $this->api('GET', '/v1/media/'.$media['id']);
        $this->assertTrue($this->data($shown, 'description') !== 'hack', 'unauthenticated update must not change data');
    }


    public function testUpdateMissingMediaIsNotFound(): void {
        $user = $this->create_user();

        $response = $this->api('PUT', '/v1/media/2147483000', ['description' => 'x'], $user['token']);
        $this->assertApiError(404, $response);
    }


    public function testUserCannotUpdateForeignMedia(): void {
        $owner = $this->create_user();
        $stranger = $this->create_user();
        $media = $this->upload_png($owner);

        $response = $this->api('PUT', '/v1/media/'.$media['id'], ['description' => 'stolen'], $stranger['token']);
        $this->assertApiError(403, $response);

        $shown = $this->api('GET', '/v1/media/'.$media['id']);
        $this->assertTrue($this->data($shown, 'description') !== 'stolen', 'foreign update must not change data');
    }


    public function testModeratorCanUpdateForeignMedia(): void {
        $owner = $this->create_user();
        $media = $this->upload_png($owner);

        $response = $this->api('PUT', '/v1/media/'.$media['id'], ['description' => 'moderated'], $this->token_for('moderator'));
        $this->assertApiSuccess($response);
        $this->assertSame('moderated', $this->data($response)['media']['description']);
    }


    // ---------------------------------------------------------------- delete

    public function testDeleteByOwnerRemovesMedia(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        $response = $this->api('DELETE', '/v1/media/'.$media['id'], null, $user['token']);
        $this->assertApiSuccess($response);
        $this->assertSame((int)$media['id'], (int)$this->data($response, 'id'));

        $this->assertApiError(404, $this->api('GET', '/v1/media/'.$media['id']));
        $this->assertApiError(404, $this->api('GET', '/v1/media/'.$media['id'].'/file'));
        $this->assertApiError(404, $this->api('DELETE', '/v1/media/'.$media['id'], null, $user['token']), 'second delete:');
    }


    public function testDeleteRequiresAuthentication(): void {
        $user = $this->create_user();
        $media = $this->upload_png($user);

        $this->assertApiError(401, $this->api('DELETE', '/v1/media/'.$media['id']));
        $this->assertApiSuccess($this->api('GET', '/v1/media/'.$media['id']), 200, 'media must still exist:');
    }


    public function testDeleteMissingMediaIsNotFound(): void {
        $user = $this->create_user();

        $this->assertApiError(404, $this->api('DELETE', '/v1/media/2147483000', null, $user['token']));
    }


    public function testUserCannotDeleteForeignMedia(): void {
        $owner = $this->create_user();
        $stranger = $this->create_user();
        $media = $this->upload_png($owner);

        $response = $this->api('DELETE', '/v1/media/'.$media['id'], null, $stranger['token']);
        $this->assertApiError(403, $response);

        $this->assertApiSuccess($this->api('GET', '/v1/media/'.$media['id']), 200, 'media must still exist:');
    }


    public function testModeratorCanDeleteForeignMedia(): void {
        $owner = $this->create_user();
        $media = $this->upload_png($owner);

        $response = $this->api('DELETE', '/v1/media/'.$media['id'], null, $this->token_for('moderator'));
        $this->assertApiSuccess($response);
        $this->assertApiError(404, $this->api('GET', '/v1/media/'.$media['id']));
    }
}
