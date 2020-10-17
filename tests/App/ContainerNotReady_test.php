<?php

    namespace Sourcegr\Tests\App;

    use Sourcegr\Framework\App\Container;
    use PHPUnit\Framework\TestCase;
    use Sourcegr\Stub\App\Car;
    use Sourcegr\Stub\App\CarInterface;
    use Sourcegr\Stub\App\Moto;
    use Sourcegr\Stub\App\MotoInterface;

    class ContainerNotReadytest extends TestCase
    {
        public function testContainer(): int
        {

            $container = new Container();

            $container->bind(MotoInterface::class, Moto::class);
            $container->bind(CarInterface::class, Car::class);

            $car = $container->make(CarInterface::class);
            var_dump(($car));
        }
    }
