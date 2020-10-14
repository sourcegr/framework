<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Request;


    use Sourcegr\Framework\Http\Request\File\UploadedFile;
    use Sourcegr\Framework\Interfaces\Http\RequestInterface;
    use stdClass;

    class HttpRequest implements RequestInterface
    {
        const METHOD_HEAD = 'HEAD';
        const METHOD_GET = 'GET';
        const METHOD_POST = 'POST';
        const METHOD_PUT = 'PUT';
        const METHOD_PATCH = 'PATCH';
        const METHOD_DELETE = 'DELETE';
        const METHOD_OPTIONS = 'OPTIONS';


        protected $getBag;
        protected $postBag;
        protected $cookieBag;
        protected $fileBag;
        protected $serverBag;

        public $data;

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

        public static function fromHTTP(): HttpRequest
        {
            $url = explode('?', $_SERVER['REQUEST_URI'])[0];
            $request = new static($url, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
            return $request;
        }


        public function __construct(string $url='', array $get = [], array $post = [], array $cookie = [], array $files = [], array $server = [])
        {
            $this->url = trim($url, "/");
            $this->getBag = new GETParameterBag($get);
            $this->postBag = new POSTParameterBag($post);
            $this->cookieBag = new COOKIEParameterBag($cookie);
            $this->fileBag = new FILEParameterBag(
                $this->createFileBag($files)
            );
            $this->serverBag = new SERVERParameterBag($server);
            $this->method = strtoupper($this->serverBag->get('REQUEST_METHOD', 'GET'));
            $this->data = new StdClass();
        }

        public function URLStartsWith(string $search): bool
        {
            return strpos($search, $this->url) === 0;
        }

        public function expectsJson():bool {
            $accepts = explode(',',  ($this->serverBag->get('HTTP_ACCEPT') ?? ''));
            return $accepts[0] === 'application/json';
        }

        public function getMethod(): string
        {
            return $this->method;
        }

        public function get(string $var, string $type = null) : ?string
        {
            if (!$type) {
                $type = 'POST';
            }
        }

        public function files(): array
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
    }