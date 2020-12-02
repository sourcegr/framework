<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\App;


    use Sourcegr\Framework\Base\ServiceProviderInterface;
    use Sourcegr\Framework\Base\View\Renderable;
    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\Redirect\Redirect;
    use Sourcegr\Framework\Http\Request\RequestInterface;
    use Sourcegr\Framework\Http\Response\ResponseInterface;

    class App implements AppInterface
    {
        /**
         * @var array $serviceProviders holds the instances of the service providers
         */
        protected $serviceProviders = [];

        /**
         * @var array $loadedProviders holds classNames for loaded service providers
         */
        protected $loadedProviders = [];

        /**
         * @var array $bootedProviders holds BOOTED service provider instances
         */
        protected $bootedProviders = [];

        /**
         * @var RequestInterface
         */

        /**
         * @var array $bootedMiddleware holds BOOTED middleware instances
         */
        protected $bootedMiddleware = [];


        /**
         * @var array $shutDownCallbacks holds callbacks to call after the route has been parsed
         */
        protected $shutDownCallbacks = [];

        /**
         * @var RequestInterface $request
         */
        public $request;


        /**
         * @var ResponseInterface $response
         */
        public $response;


        /**
         * @var ContainerInterface the container instance
         */
        public $container;


        /**
         * @var mixed
         */
        public $appConfig;


        protected function markServiceProviderAsRegistered(ServiceProviderInterface $provider)
        {
            $this->serviceProviders[] = $provider;
            $this->loadedProviders[get_class($provider)] = true;
        }


        protected function getPath(string $var): string
        {
            return __DIR__ . DIRECTORY_SEPARATOR . $var;
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

            // or save some CPU cycles since we only want DI in @boot method
            return new $provider($this->container);
        }





        public function middlewareBooted($middleware) {
            return in_array($middleware, $this->bootedMiddleware);
        }



        public function getShutDownCallbacks(): array
        {
            return $this->shutDownCallbacks;
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


        public function registerShutdownCallback($callback)
        {
            if (is_callable($callback)) {
                $this->shutDownCallbacks[] = $callback;
            }
        }

        public function prepareForShutdown()
        {
            foreach ($this->getShutDownCallbacks() as $callback) {
                $this->container->call($callback);
            }
        }

        public function shutDown()
        {
//            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (7 * 24 * 60 * 60))); // 1 week
//            header("Cache-Control: no-cache");
//            header("Pragma: no-cache");


            $response = $this->response->makeResponse();
//            dd("I DIE!", $response);

            http_response_code($this->response->statusCode);

            foreach ($this->response->headers as $headerName => $headerValue) {

                header("$headerName: $headerValue");
            }
//dd($this->request->session->getToken());


            if ($response instanceof Redirect) {
                die();
            }

            if ($response instanceof Boom) {
//                dd('will shut now');
                if ($this->request->expectsJson()) {
                    die(json_encode($response, JSON_UNESCAPED_UNICODE));
                }
                die($response->message);
            }

            die($response);
        }
    }