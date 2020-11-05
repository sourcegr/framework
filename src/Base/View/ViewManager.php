<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\View;


    use Sourcegr\Framework\Base\Helpers\Arr;

    class ViewManager
    {
        protected $namespaces = [];

        protected $templatesDir = null;
        protected $cacheDir = null;
        protected $globals = [];

        public function __construct($templatesDir, $cacheDir)
        {
            $this->namespaces['DEFAULT'] = $templatesDir;
            $this->cacheDir = $cacheDir;
        }


        public function addNamespace($ns, $templatesDir) {
            $this->namespaces[$ns] = $templatesDir;
        }

        public function make($file) {
            if (!strpos($file, '::')) {
                $file = "DEFAULT::$file";
            }

            [$ns, $file] = explode('::', $file);

            if (!$ns) {
                throw new \Exception('Namespace not found');
            }

            $appx = new View($this->namespaces[$ns], $this->cacheDir, $file);
            $appx->with($this->globals);
            return $appx;
        }


        public function addGlobal($name, $value=null)
        {
            if (Arr::is($name)) {
                foreach ($name as $key => $value) {
                     $this->addGlobalParam($key, $value);
                }

                return $this;
            }

            return $this->addGlobalParam($name, $value);
        }


        public function addGlobalParam($name, $value=null) {
            $this->globals[$name] = $value;
            return $this;
        }

    }