<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base;


    use Sourcegr\Framework\Base\Helpers\Arr;
    use Sourcegr\Framework\Base\Interfaces\App;

    class Kernel
    {
        private $serviceProviders = [];
        private $middlewares = [];

        public $app;

        public function __construct($app)
        {
            $this->app = $app;
        }

        public function registerServiceProviders($config)
        {
            $services = [];
            $isProduction = $this->app->env == 'production';

            foreach ($config as $name => $serviceProviderDefinition) {
                if (($this->serviceProviders[$name] ?? null) && $isProduction) {
                    throw new \Exception('cannot overwrite provider in production');
                }

                $this->serviceProviders[$name] = $serviceProviderDefinition;

                if (($serviceProviderDefinition['immediate'] ?? false)) {
                    // initialize now
                    $services[$name] = $this->initServiceProvider($serviceProviderDefinition['class']);
                }
            }

            return $services;
        }

        public function initServiceProviders()
        {
            foreach ($this->serviceProviders as $name => $serviceProviderDefinition) {
                if (!($serviceProviderDefinition['immediate'] ?? false)) {
                    $res = $this->initServiceProvider($serviceProviderDefinition['class']);
                    if ($res) {
                        if (Arr::is($res)) {
                            $tag = Arr::keys($res)[0];

                            $this->app->serviceInited(
                                $name,
                                $res[$tag],
                                $tag
                            );
                        } else {
                            $this->app->serviceInited($name, $res);
                        }
                    }
                }
            }
        }

        private function initServiceProvider($serviceProvider)
        {
            return (new $serviceProvider($this->app))->init();
        }


        public function registerMiddlewares($config)
        {
            $middlewares = [];

            foreach ($config as $name => $definition) {
                $this->middlewares[$name] = $definition;

                $onInit = $definition['immediate'] ?? false;
                if (!$onInit) {
                    // initialize now
                    $middlewares[$name] = $this->initMiddleware($definition);
                }
            }

            return $middlewares;
        }

        public function initMiddlewares()
        {
            $middlewares = [];

            foreach ($this->middlewares as $name => $definition) {
                if (!($definition['immediate'] ?? false)) {
                    $middlewares[$name] = $this->initMiddleware($definition['class']);
                }
            }

            return $middlewares;
        }

        private function initMiddleware($middleware)
        {
            return (new $middleware($this->app))->init();
        }
    }