<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Request;


    use Sourcegr\Framework\Base\Auth\Guard\GuardInterface;
    use Sourcegr\Framework\Base\Helpers\Helpers;
    use Sourcegr\Framework\Base\ParameterBag;
    use Sourcegr\Framework\Http\Request\File\UploadedFile;
    use Sourcegr\Framework\Http\Session\SessionBag;


    class HttpRequest implements RequestInterface
    {
        public const METHOD_HEAD = 'HEAD';
        public const METHOD_GET = 'GET';
        public const METHOD_POST = 'POST';
        public const METHOD_PUT = 'PUT';
        public const METHOD_PATCH = 'PATCH';
        public const METHOD_DELETE = 'DELETE';
        public const METHOD_OPTIONS = 'OPTIONS';


        protected $varsBag;
        protected $fileBag;
        protected $serverBag;
        protected $headerBag;
        protected $accepts;

        protected $expectsJSON = false;

        /**
         * the session data
         *
         * @var SessionBag $session
         */
        public $session = null;

        /**
         * the cookie data
         *
         * @var COOKIEParameterBag $cookie
         */
        public $cookie;


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
         * @var GuardInterface $auth
         */
        public $auth = null;

        /**
         * @var $user
         */
        public $user = null;


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

            $this->cookie = new COOKIEParameterBag($cookie);
            $this->fileBag = new FILEParameterBag($this->createFileBag($files));
            $this->serverBag = new SERVERParameterBag($server);
            $this->headerBag = new ParameterBag(Helpers::getRequestHeaders());

            $this->method = strtoupper($this->serverBag->get('REQUEST_METHOD', 'GET'));
            $this->createAccepts();
        }

        protected function createAccepts()
        {
            $all = explode(',', str_replace(' ', '', $this->serverBag->get('HTTP_ACCEPT') ?? ''));


            foreach ($all as $accept) {
                $accept = "$accept;q=1";
                [$type, $quality] = explode(';q=', $accept);
                $accepts[$type] = $quality;
            }
//            dd($accepts);

            arsort($accepts, SORT_NUMERIC);
            $this->accepts = $accepts;
            $this->expectsJSON = isset($accepts['application/json']) ? true : false;
        }

        public static function fromHTTP(): RequestInterface
        {
            $url = explode('?', $_SERVER['REQUEST_URI'])[0];
            $request = new static($url, $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);


            return $request;
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


        public function setRealm(string $realm): RequestInterface
        {
            $this->realm = $realm;
            return $this;
        }

        public function expectsJson(): bool
        {
            return $this->expectsJSON;
        }

        public function getMethod(): string
        {
            return $this->method;
        }

        public function getHeader(string $header = null)
        {
            return $this->headerBag->get($header) ?? null;
        }

        public function all()
        {
            return $this->varsBag;
        }

        public function allGET()
        {
            return $this->all()['GET'];
        }
        public function allPOST()
        {
            return $this->all()['POST'];
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

            return $this->varsBag[$type] ? ($this->varsBag[$type]->get($key) ?? null) : null;
        }

        public function files($key = null)
        {
            if ($key) {
                $f = $this->fileBag->get($key);
                if (!is_array($f)) {
                    return null;
                }
                if (count($f) == 1) {
                    return $f[0];
                }

                return $f;
            }
            return $this->fileBag->values();
        }

        public function persistSession()
        {
            if (!$this->session) {
                return;
            }

            $this->session->setPreviousUrl($this->url);
            $this->session->expireFlashData();
            $this->session->persist();
        }

        public function flash(string $name, $value)
        {
            $this->session->setFlash($name, $value);
        }

        public function addFlash($flashNameOrArray, $flashData = null)
        {
            if (!is_null($flashData)) {
                $flashNameOrArray = [$flashNameOrArray => $flashData];
            }

            foreach ($flashNameOrArray as $name => $value) {
                $this->session->addFlash($name, $value);
            }

            return $this;
        }

        public function getBearerToken()
        {
            $authHeader = $this->headerBag->get('Authorization');

            if (!$authHeader) {
                return null;
            }

            if (strpos($authHeader, 'Bearer ') === 0) {
                return substr($authHeader, 7);
            }

            return null;
        }
    }