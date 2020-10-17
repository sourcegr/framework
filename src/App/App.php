<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\App;



    use Sourcegr\Framework\Base\ServiceProviderInterface;
    use Sourcegr\Framework\Http\Request\RequestInterface;

    class App implements AppInterface
    {
        /**
         * @var array holds the instances of the service providers
         */
        protected $serviceProviders = [];

        /**
         * @var array holds classNames for loaded service providers
         */
        protected $loadedProviders = [];

        /**
         * @var array holds BOOTED service provider instances
         */
        protected $bootedProviders = [];

        /**
         * @var RequestInterface
         */

        /**
         * @var array holds BOOTED middleware instances
         */
        protected $bootedMiddleware = [];

        protected $request;
        /**
         * @var ContainerInterface the container instance
         */
        public $container;



        public function getRequest(): RequestInterface
        {
            return $this->request;
        }


        protected function getPath(string $var): string
        {
            return __DIR__ . DIRECTORY_SEPARATOR . $var;
        }


        public function __construct()
        {
            $this->container = new Container($this);
        }


        public function isDownForMaintenance()
        {
            return false;
        }


        public function loadAppConfig($key)
        {
            return $this->loadConfig('app', $key);
        }


        public function loadConfig($file, $key = null)
        {

            $config = $this->loadConfigFile($this->getPath('CONFIG') . $file);
            return is_null($key) ? $config : $config[$key];
        }


        public function loadConfigFile($file, $key = null)
        {
            $config = require "$file.php";
            return is_null($key) ? $config : $config[$key];
        }


        protected function registerServiceProvider($provider)
        {
            if (is_string($provider)) {
                $provider = $this->getServiceProviderInstance($provider);
            }

            // run register on provider instance
            $provider->register();

            // mark registered
            $this->markServiceProviderAsRegistered($provider);
        }


        public function execServiceProviders()
        {
            $groups = func_get_args();

            foreach ($groups as $providers) {
                //ensure it is an array
                if (!is_array($providers)) {
                    $providers = [$providers];
                }
                array_walk($providers, [$this, 'registerServiceProvider']);
            }
            $this->bootServiceProviders();
        }


        public function bootServiceProviders()
        {
            array_walk($this->serviceProviders, [$this, 'bootServiceProvider']);
        }



        public function execMiddleware()
        {
            $groups = func_get_args();

            foreach ($groups as $middlewares) {
                //ensure it is an array
                if (!is_array($middlewares)) {
                    $middlewares = [$middlewares];
                }
                foreach ($middlewares as $middleware) {
                    $this->runMiddleware($middleware);
                }
//                array_walk($middlewares, [$this, 'runMiddleware', ]);
            }
        }


        protected function runMiddleware($middleware)
        {
            if (in_array($middleware, $this->bootedMiddleware)) {
                return;
            }

            $this->container->call("$middleware@handle");

            $this->bootedMiddleware[] = $middleware;
        }


        protected function bootServiceProvider($provider)
        {
            if (in_array($provider, $this->bootedProviders)) {
                return;
            }

            $this->container->call([$provider, 'boot']);

            $this->bootedProviders[] = $provider;
        }


        protected function getServiceProviderInstance(string $provider)
        {
            // use the method bellow to use dependency injection
            // return $this->container->make($provider);

            // or save some CPU-cycles since we only want DI in @boot method
            return new $provider($this->container);
        }


        protected function markServiceProviderAsRegistered(ServiceProviderInterface $provider)
        {
            $this->serviceProviders[] = $provider;
            $this->loadedProviders[get_class($provider)] = true;
        }
    }