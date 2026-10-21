<?php declare(strict_types=1);

class SanitizeTest extends TestCase {

    public function testSanitizeStringStripsTagsAndTrims(): void {
        $this->assertSame('hello', sanitize_string('  <b>hello</b> '));
    }


    /**
     * All API input goes through strip_tags: it is the only barrier for values
     * that later reach x-html. No "<" must survive when it could open a tag.
     */
    public function testInputDataLeavesNoTags(): void {
        $payloads = [
            '<script>alert(1)</script>',
            '<img src=x onerror=alert(1)>',
            '<img src=x onerror=alert(1)',
            '<<b>b>text',
            'name<svg/onload=alert(1)>',
        ];

        foreach($payloads as $payload) {
            $clean = sanitize_input_data($payload);
            $this->assertFalse(str_contains($clean, '<'), 'Tag survived for payload: '.$payload.' => '.$clean);
        }
    }


    public function testInputDataIsRecursiveAndCleansKeys(): void {
        $clean = sanitize_input_data([
            '<i>key</i>' => ' <b>value</b> ',
            'nested' => ['a' => '<u>x</u>'],
            'number' => 5,
            'flag' => true,
            'nothing' => null,
        ]);

        $this->assertSame(
            ['key' => 'value', 'nested' => ['a' => 'x'], 'number' => 5, 'flag' => true, 'nothing' => null],
            $clean
        );
    }


    public function testPlainLessThanWithSpaceIsKept(): void {
        // strip_tags is not an escaper: "a < b" is plain text and stays as is
        $this->assertSame('a < b', sanitize_input_data('a < b'));
    }


    public function testSanitizePathNormalPaths(): void {
        $this->assertSame('/', sanitize_path('/'));
        $this->assertSame('/ok/page', sanitize_path('/ok/page'));
        $this->assertSame('/!/acp/debug', sanitize_path('/!/acp/debug'));
        $this->assertSame('/page', sanitize_path('/page?x=1'));
    }


    public function testSanitizePathRemovesTraversal(): void {
        foreach(['/a/../b', '/a/..%2fb', '/%2e%2e/etc/passwd', '/%252e%252e/etc/passwd', '/....//x'] as $uri) {
            $path = sanitize_path($uri);
            $this->assertTrue($path === false || !str_contains($path, '..'), 'Traversal left in: '.$uri.' => '.var_export($path, true));
        }
    }


    public function testSanitizePathRejectsScriptsAndControlChars(): void {
        $this->assertFalse(sanitize_path('/shell.php'));
        $this->assertFalse(sanitize_path('/run.sh'));
        $this->assertFalse(sanitize_path('/a%00b'));
    }


    public function testSanitizeFilename(): void {
        $this->assertSame('passwd', sanitize_filename('../../etc/passwd'));
        $this->assertSame('my_file.txt', sanitize_filename('my file.txt'));
        $this->assertSame('a.b.txt', sanitize_filename('a..b.txt'));
        $this->assertSame('htaccess', sanitize_filename('.htaccess'));
        $this->assertSame('unknown_file', sanitize_filename(''));
    }


    public function testFilenameExtensionIsLowercase(): void {
        $this->assertSame('jpg', sanitize_filename_extension('Photo.JPG'));
        $this->assertSame('', sanitize_filename_extension('noextension'));
    }


    public function testIsValidEmail(): void {
        $this->assertTrue(is_valid_email('user@example.com'));
        $this->assertFalse(is_valid_email('user@'));
        $this->assertFalse(is_valid_email('not an email'));
    }


    public function testIsValidId(): void {
        $this->assertTrue(is_valid_id('5'));
        $this->assertTrue(is_valid_id(12));
        $this->assertFalse(is_valid_id('0'));
        $this->assertFalse(is_valid_id('-3'));
        $this->assertFalse(is_valid_id('abc'));
        $this->assertFalse(is_valid_id(null));
    }


    public function testSafeJsonDecode(): void {
        $this->assertSame(['a' => 1], safe_json_decode('{"a":1}'));
        $this->assertNull(safe_json_decode('"just a string"'));
        $this->assertNull(safe_json_decode('{"a":1}', 3), 'Body over the size limit must give null');
    }


    /**
     * The signature is ?array and BaseController::get_input_data() checks "=== null",
     * but json_decode() is called with JSON_THROW_ON_ERROR, so broken JSON throws JsonException.
     */
    public function testSafeJsonDecodeReturnsNullOnBrokenJson(): void {
        $result = null;

        try {
            $result = safe_json_decode('{broken');
        } catch(JsonException $e) {
            $this->fail('safe_json_decode() threw JsonException instead of returning null');
        }

        $this->assertNull($result);
    }


    public function testIsJsonString(): void {
        $this->assertTrue(is_json_string('{"a":1}'));
        $this->assertTrue(is_json_string('[1,2]'));
        $this->assertFalse(is_json_string('{broken'));
        $this->assertFalse(is_json_string('plain'));
        $this->assertFalse(is_json_string(''));
    }
}
