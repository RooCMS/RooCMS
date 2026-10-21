<?php declare(strict_types=1);

class UserValidationTest extends TestCase {

    private UserValidationService $validator;


    protected function setUp(): void {
        // validate_website() does not use dependencies: skip the constructor (it needs a database)
        $this->validator = (new ReflectionClass(UserValidationService::class))->newInstanceWithoutConstructor();
    }


    public function testWebLinksAreAccepted(): void {
        foreach(['https://example.com', 'http://example.com/path?x=1#top', 'HTTPS://EXAMPLE.COM'] as $url) {
            $this->validator->validate_website($url);
        }

        $this->assertTrue(true);
    }


    public function testEmptyWebsiteIsAllowed(): void {
        $this->validator->validate_website(null);
        $this->validator->validate_website('');

        $this->assertTrue(true);
    }


    public function testDangerousAndNonWebSchemesAreRejected(): void {
        $urls = [
            'javascript://example.com/%0Aalert(1)',
            'ftp://example.com/file',
            'file:///etc/passwd',
            'data:text/html,hello',
            'mailto:user@example.com',
        ];

        foreach($urls as $url) {
            $this->assertThrows(DomainException::class, fn() => $this->validator->validate_website($url), 422, 'Accepted: '.$url);
        }
    }


    public function testGarbageIsRejected(): void {
        $this->assertThrows(DomainException::class, fn() => $this->validator->validate_website('not a url'), 422);
        $this->assertThrows(DomainException::class, fn() => $this->validator->validate_website('example.com'), 422);
    }
}
