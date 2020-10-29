<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\View;


    use Sourcegr\Framework\Base\Helpers\Arr;
    use Sourcegr\Framework\Base\Helpers\Str;

    class View implements Renderable
    {
        public $viewsDir = __DIR__ . '/views';
        public $cacheDir = __DIR__ . '/cache';

        protected $params;
        protected $cachedFile;
        protected $template = '';


        protected static $map = [
            '{!' => '<?=',
            '!}' => '?>',
            '{{' => '<?=htmlspecialchars(',
            '}}' => ', ENT_NOQUOTES, \'UTF-8\', true)?>',
            '@endforeach' => '<?php endforeach; ?>',
            '@endif' => '<?php endif; ?>',
            '@end' => '<?php endif; ?>',
            '@elseif' => '<?php elseif : ?>',
            '@else' => '<?php else : ?>',
        ];


        public function __construct($viewsDir, $cacheDir, $filename, $isSubView = false)
        {
            $this->viewsDir = $viewsDir ?? $this->viewsDir;
            $this->cacheDir = $cacheDir ?? $this->cacheDir;
            $this->template = $this->make($filename, $isSubView);
        }

        protected function includes(string $contents)
        {
            $contents = preg_replace_callback('/@include\([\'"](.*)[\'"]\)/', [$this, 'callbackInclude'], $contents);
            return $contents;
        }


        protected function callbackInclude($matches)
        {
            $filename = $matches[1];
            $inc = new static($this->viewsDir, $this->cacheDir, $filename, true);
            return $inc->getTemplate();
        }


        protected function each($contents)
        {
            $contents = preg_replace('/@foreach(.*)/', '<?php foreach ${1} :?>', $contents);
            $contents = preg_replace('/@elseif(.*)/', '<?php elseif ${1} :?>', $contents);
            $contents = preg_replace('/@if(.*)/', '<?php if ${1} :?>', $contents);
            $contents = preg_replace('/@isset(.*)/', '<?php if (isset${1}) :?>', $contents);
            $contents = preg_replace('/@empty(.*)/', '<?php if (!isset${1}) :?>', $contents);
            return $contents;
        }


        public function getTemplate()
        {
            return $this->template;
        }


        public function make(string $viewName, $isSubTemplate = false)
        {
            $contents = file_get_contents($this->viewsDir . DIRECTORY_SEPARATOR . $viewName . '.blade.php');
            $contents = $this->includes($contents);
            $contents = $this->each($contents);
            $contents = str_replace(array_keys(static::$map),
                array_values(static::$map),
                $contents);

            if ($isSubTemplate) {
                return $contents;
            }

            $this->cachedFile = $this->cacheDir . DIRECTORY_SEPARATOR . $viewName . '.blade.php';
            file_put_contents($this->cachedFile, $contents);

            return $this;
        }


        public function with($name, $value = null)
        {
            if (Arr::is($name)) {
                foreach ($name as $key => $value) {
                    $this->addParam($key, $value);
                }

                return $this;
            }

            return $this->addParam($name, $value);
        }

        protected function addParam($name, $value = null)
        {
            $this->params[$name] = $value;
            return $this;
        }


        public function getOutput(...$params) : string
        {
            extract($this->params ?? []);

            ob_start();
            include $this->cachedFile;

            $contents = ob_get_contents();
            ob_end_clean();

            return $contents;
        }


        public function render(...$params): string
        {
            echo $this->getOutput(...$params);
        }
    }