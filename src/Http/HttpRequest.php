<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http;


    use Sourcegr\Framework\Http\Request\COOKIEParameterBag;
    use Sourcegr\Framework\Http\Request\File\UploadedFile;
    use Sourcegr\Framework\Http\Request\FILEParameterBag;
    use Sourcegr\Framework\Http\Request\GETParameterBag;
    use Sourcegr\Framework\Http\Request\POSTParameterBag;
    use Sourcegr\Framework\Http\Request\SERVERParameterBag;

    class HttpRequest
    {
        const METHOD_HEAD = 'HEAD';
        const METHOD_GET = 'GET';
        const METHOD_POST = 'POST';
        const METHOD_PUT = 'PUT';
        const METHOD_PATCH = 'PATCH';
        const METHOD_DELETE = 'DELETE';
        const METHOD_OPTIONS = 'OPTIONS';

        protected $getBag = null;
        protected $postBag = null;
        protected $cookieBag = null;
        protected $fileBag = null;
        protected $serverBag = null;

        public $method = null;


        public function __construct($get = [], $post = [], $cookie = [], $files = [], $server = [])
        {
            $this->getBag = new GETParameterBag($get);
            $this->postBag = new POSTParameterBag($post);
            $this->cookieBag = new COOKIEParameterBag($cookie);
            $this->fileBag = new FILEParameterBag(
                $this->createFileBag($files)
            );
            $this->serverBag = new SERVERParameterBag($server);

            $this->method = strtoupper($this->serverBag->get('REQUEST_METHOD', 'GET'));
        }

        public static function fromHTTP()
        {
            return new static($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
        }

        public function getMethod()
        {
            return $this->method;
        }

        public function get($var, $type = null)
        {
            if ($type) {
            }
        }

        public function files()
        {
            return $this->fileBag->values();
        }


        protected function createFileBag($files)
        {
            $normalized = [];

            foreach ($files as $index => $file) {
                if (!is_array($file['name'])) {
                    $normalized[$index][] = new UploadedFile($file);
                    continue;
                }

                foreach ($file['name'] as $index => $name) {
                    $normalized[$index][$index] = new UploadedFile([
                        'name' => $name,
                        'type' => $file['type'][$index],
                        'tmp_name' => $file['tmp_name'][$index],
                        'error' => $file['error'][$index],
                        'size' => $file['size'][$index]
                    ]);
                }
            }

            return $normalized;
        }
    }