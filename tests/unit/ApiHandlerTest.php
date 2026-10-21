<?php declare(strict_types=1);

class ApiHandlerTest extends TestCase {

    private ApiHandler $api;


    protected function setUp(): void {
        $controllers = new class implements ControllerFactory {
            public function create(string $controllerClass): object {
                return new stdClass();
            }
        };

        $middleware = new class implements MiddlewareFactory {
            public function create(string $middlewareClass): object {
                return new class {
                    public function handle(): bool { return false; }
                    public function allow(): bool { return true; }
                };
            }
        };

        $this->api = new ApiHandler($controllers, $middleware);
    }


    // NOTE: dispatch() of an unknown route calls output_json() (exit), so "not found" is checked via route_exists()


    public function testNumericPlaceholderMatchesOnlyDigits(): void {
        $this->api->get('/v1/users/{user_id}', fn() => 'ok');

        $this->assertTrue($this->api->route_exists('GET', '/v1/users/42'));
        $this->assertFalse($this->api->route_exists('GET', '/v1/users/abc'));
        $this->assertFalse($this->api->route_exists('GET', '/v1/users/42/extra'));
        $this->assertFalse($this->api->route_exists('GET', '/v1/users/'));
    }


    public function testIdPlaceholderMatchesOnlyDigits(): void {
        $this->api->get('/v1/media/{id}', fn() => 'ok');

        $this->assertTrue($this->api->route_exists('GET', '/v1/media/7'));
        $this->assertFalse($this->api->route_exists('GET', '/v1/media/seven'));
    }


    public function testNamedPlaceholderMatchesSingleSegment(): void {
        $this->api->get('/v1/structure/page/slug/{slug}', fn() => 'ok');

        $this->assertTrue($this->api->route_exists('GET', '/v1/structure/page/slug/about-us'));
        $this->assertFalse($this->api->route_exists('GET', '/v1/structure/page/slug/a/b'));
    }


    public function testMethodMustMatch(): void {
        $this->api->post('/v1/auth/login', fn() => 'ok');

        $this->assertTrue($this->api->route_exists('POST', '/v1/auth/login'));
        $this->assertFalse($this->api->route_exists('GET', '/v1/auth/login'));
        $this->assertFalse($this->api->route_exists('DELETE', '/v1/auth/login'));
    }


    public function testTrailingSlashAndQueryStringAreIgnored(): void {
        $this->api->get('/v1/health', fn() => 'ok');

        $this->assertTrue($this->api->route_exists('GET', '/v1/health/'));
        $this->assertTrue($this->api->route_exists('GET', '/v1/health?x=1'));
    }


    public function testDispatchPassesParametersToHandler(): void {
        $this->api->get('/v1/users/{user_id}/posts/{slug}', fn($user_id, $slug) => $user_id.':'.$slug);

        $this->assertSame('42:hello', $this->api->dispatch('GET', '/v1/users/42/posts/hello'));
    }


    public function testFirstMatchingRouteWins(): void {
        // the same order as in api/router.php: literal route first, then the numeric one
        $this->api->get('/v1/acp/users/roles', fn() => 'roles');
        $this->api->get('/v1/acp/users/{user_id}', fn($id) => 'user:'.$id);

        $this->assertSame('roles', $this->api->dispatch('GET', '/v1/acp/users/roles'));
        $this->assertSame('user:7', $this->api->dispatch('GET', '/v1/acp/users/7'));
    }


    public function testNumericRouteDoesNotSwallowLiteralRouteDeclaredLater(): void {
        // PUT /acp/structure/{id} is declared before PUT /acp/structure/reorder in router.php:
        // {id} is digits only, so "reorder" is still reachable
        $this->api->put('/v1/acp/structure/{id}', fn($id) => 'update:'.$id);
        $this->api->put('/v1/acp/structure/reorder', fn() => 'reorder');

        $this->assertSame('reorder', $this->api->dispatch('PUT', '/v1/acp/structure/reorder'));
        $this->assertSame('update:3', $this->api->dispatch('PUT', '/v1/acp/structure/3'));
    }


    public function testMiddlewareThatReturnsFalseStopsHandler(): void {
        $called = false;
        $this->api->get('/v1/secret', function() use (&$called) {
            $called = true;
            return 'secret';
        }, ['Block']);

        $this->assertFalse($this->api->dispatch('GET', '/v1/secret'));
        $this->assertFalse($called);
    }


    public function testMiddlewareMethodSyntax(): void {
        $this->api->get('/v1/allowed', fn() => 'passed', ['Guard@allow']);

        $this->assertSame('passed', $this->api->dispatch('GET', '/v1/allowed'));
    }


    public function testGetRoutesReturnsRegisteredRoutes(): void {
        $this->api->get('/a', fn() => 1);
        $this->api->post('/b', fn() => 2, ['Block']);

        $routes = $this->api->get_routes();

        $this->assertSame(2, count($routes));
        $this->assertSame('POST', $routes[1]['method']);
        $this->assertSame(['Block'], $routes[1]['middleware']);
    }
}
