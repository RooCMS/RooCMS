# RooCMS tests

Tests without external dependencies (no composer, no PHPUnit): a tiny runner written in pure PHP, in line with the framework-free approach of the project. CLI only: the folder is closed from the web by `.htaccess` and `bootstrap.php` refuses to run outside of CLI.

## Run

```
php tests/run.php              # unit tests (default)
php tests/run.php http         # HTTP tests against a running instance
php tests/run.php all          # both suites
php tests/run.php unit Role    # only tests whose "Class::method" contains "Role"
```

Exit code is `0` if everything passed, `1` if something failed, `2` for a wrong suite name.

## Structure

```
tests/
├── bootstrap.php      # constants and includes; no configs, no database
├── TestCase.php       # base class with assertions
├── run.php            # runner
├── unit/              # pure logic, work offline
└── http/              # requests to a running server
```

## Unit tests

Cover code that does not need a database: `Role`, `sanitize.php` helpers, `DependencyContainer`, route matching in `ApiHandler`, `UserValidationService::validate_website()`.

Classes that need `Db` are not covered yet: they need a test double for the database layer.

## HTTP tests

Read-only, no credentials: they never create accounts or change data.

| Variable | Default | Purpose |
|---|---|---|
| `ROOCMS_TEST_URL` | `https://dev.roocms.com` | address of the instance under test |
| `ROOCMS_TEST_INSECURE` | unset | `1` disables TLS verification (self-signed certificate) |

If the server is unreachable the tests are reported as `SKIP`.

## Writing a test

Create `tests/unit/SomethingTest.php` (the file name must end with `Test.php`):

```php
<?php declare(strict_types=1);

class SomethingTest extends TestCase {

    public function testItWorks(): void {
        $this->assertSame(4, 2 + 2);
    }
}
```

Test methods are public methods starting with `test`. A fresh object is created for every method, `setUp()` runs before each one. Available assertions: `assertTrue`, `assertFalse`, `assertNull`, `assertSame`, `assertContains`, `assertStringContains`, `assertStringNotContains`, `assertThrows`, plus `fail()` and `skip()`.
