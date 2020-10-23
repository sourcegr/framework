<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base;


    use Sourcegr\Framework\App\ContainerInterface;

    abstract class ServiceProvider implements ServiceProviderInterface
    {
        /**
         * @var ContainerInterface $container
         */
        public $container = null;

        public function __construct(ContainerInterface $container)
        {
            $this->container = $container;
        }


//        abstract public function register();
//        abstract public function boot(...$params);

        public function loadConfig($config)
        {
            return \app()->loadConfig($config);
        }

        public function registerSomethingElse()
        {
        }
    }