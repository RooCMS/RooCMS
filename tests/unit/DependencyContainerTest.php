<?php declare(strict_types=1);

class TestDepA {}

class TestDepB {
    public function __construct(public TestDepA $a) {}
}

class TestDepWithDefaults {
    public function __construct(public int $size = 5, public ?TestDepA $optional = null) {}
}

class TestDepNeedsBuiltin {
    public function __construct(public int $size) {}
}


class DependencyContainerTest extends TestCase {

    private DependencyContainer $container;


    protected function setUp(): void {
        $this->container = new DependencyContainer();
    }


    public function testSingletonReturnsSameInstance(): void {
        $this->container->register(TestDepA::class, TestDepA::class, true);

        $this->assertTrue($this->container->get(TestDepA::class) === $this->container->get(TestDepA::class));
    }


    public function testNonSingletonReturnsNewInstances(): void {
        $this->container->register(TestDepA::class, TestDepA::class);

        $this->assertFalse($this->container->get(TestDepA::class) === $this->container->get(TestDepA::class));
    }


    public function testAutowiresConstructorDependencies(): void {
        $this->container->register(TestDepA::class, TestDepA::class, true);
        $this->container->register(TestDepB::class, TestDepB::class);

        $b = $this->container->get(TestDepB::class);

        $this->assertTrue($b->a === $this->container->get(TestDepA::class));
    }


    public function testFactoryCallbackReceivesContainer(): void {
        $this->container->register(TestDepA::class, fn(DependencyContainer $c) => new TestDepA());
        $this->container->register('custom', fn(DependencyContainer $c) => $c->get(TestDepA::class));

        $this->assertTrue($this->container->get('custom') instanceof TestDepA);
    }


    public function testUnregisteredServiceThrows(): void {
        $this->assertThrows(Exception::class, fn() => $this->container->get('missing'));
    }


    public function testUnresolvableDependencyThrows(): void {
        // TestDepA is not registered, TestDepB needs it
        $this->container->register(TestDepB::class, TestDepB::class);

        $this->assertThrows(Exception::class, fn() => $this->container->get(TestDepB::class));
    }


    public function testDefaultsAndNullableParametersAreUsed(): void {
        $this->container->register(TestDepWithDefaults::class, TestDepWithDefaults::class);

        $instance = $this->container->get(TestDepWithDefaults::class);

        $this->assertSame(5, $instance->size);
        $this->assertNull($instance->optional);
    }


    public function testBuiltinWithoutDefaultThrows(): void {
        $this->container->register(TestDepNeedsBuiltin::class, TestDepNeedsBuiltin::class);

        $this->assertThrows(Exception::class, fn() => $this->container->get(TestDepNeedsBuiltin::class));
    }


    public function testHasAndRegisteredServices(): void {
        $this->container->register(TestDepA::class, TestDepA::class);

        $this->assertTrue($this->container->has(TestDepA::class));
        $this->assertFalse($this->container->has(TestDepB::class));
        $this->assertSame([TestDepA::class], $this->container->get_registered_services());
    }


    public function testSetInstanceIsReturnedForSingleton(): void {
        $instance = new TestDepA();
        $this->container->register(TestDepA::class, TestDepA::class, true);
        $this->container->set_instance(TestDepA::class, $instance, true);

        $this->assertTrue($this->container->get(TestDepA::class) === $instance);
    }
}
