<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Request;


    use Sourcegr\Framework\Base\Helpers\Helpers;
    use Sourcegr\Framework\Base\ParameterBag;
    use Sourcegr\Framework\Http\Request\File\UploadedFile;


    class HttpRequest implements RequestInterface
    {
        const METHOD_HEAD = 'HEAD';
        const METHOD_GET = 'GET';
        const METHOD_POST = 'POST';
        const METHOD_PUT = 'PUT';
        const METHOD_PATCH = 'PATCH';
        const METHOD_DELETE = 'DELETE';
        const METHOD_OPTIONS = 'OPTIONS';


        protected $varsBag;
        protected $cookieBag;
        protected $fileBag;
        protected $serverBag;
        protected $headerBag;

        /**
         * @var string $url
         */
        public $url = null;

        /**
         * @var string $method
         */
        public $method = null;

        /**
         * @var string $realm
         */
        public $realm = null;

        /**
         * @param string $realm
         *
         * @return RequestInterface
         */
        public function setRealm(string $realm): RequestInterface
        {
            $this->realm = $realm;
            return $this;
        }

        public static function fromHTTP(): RequestInterface
        {
            $url = explode('?', $_SERVER['REQUEST_URI'])[0];
            $request = new static($url, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
            return $request;
        }


        public function __construct(
            string $url = '',
            array $get = [],
            array $post = [],
            array $cookie = [],
            array $files = [],
            array $server = []
        ) {
            $this->url = trim($url, "/");
            $this->varsBag = [
                static::METHOD_GET => new ParameterBag($get),
                static::METHOD_POST => new ParameterBag($post)
            ];

            $this->cookieBag = new COOKIEParameterBag($cookie);
            $this->fileBag = new FILEParameterBag($this->createFileBag($files));
            $this->serverBag = new SERVERParameterBag($server);
            $this->headerBag = new ParameterBag(Helpers::getRequestHeaders());

            $this->method = strtoupper($this->serverBag->get('REQUEST_METHOD', 'GET'));
        }


        public function expectsJson(): bool
        {
            $accepts = explode(',', ($this->serverBag->get('HTTP_ACCEPT') ?? ''));
            return $accepts[0] === 'application/json';
        }


        public function getMethod(): string
        {
            return $this->method;
        }

        public function getHeader(string $header): ?string
        {
            return $this->headerBag->get($header) ?? null;
        }


        public function get(string $key, string $type = null): ?string
        {
            if (!$type) {
                if ($this->method === static::METHOD_GET || $this->method === static::METHOD_POST) {
                    $type = $this->method;
                } else {
                    $type = static::METHOD_GET;
                }
            }
//            dd($this->varsBag[$type]->get('sadfasdf'));

            return $this->varsBag[$type] ? ($this->varsBag[$type]->get($key) ?? null) : null;
        }


        public function filesArray(): array
        {
            return $this->fileBag->values();
        }


        protected function createFileBag(array $files): array
        {
            $normalized = [];

            foreach ($files as $index => $file) {
                if (!is_array($file['name'])) {
                    $normalized[$index][] = new UploadedFile($file);
                    continue;
                }

                foreach ($file['name'] as $idx => $name) {
                    $normalized[$index][$idx] = new UploadedFile([
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

        public function getSession()
        {
            // TODO: Implement getSession() method.
        }
    }