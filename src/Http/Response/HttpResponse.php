<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Response;


    use Sourcegr\Framework\Base\View\Renderable;
    use Sourcegr\Framework\Http\Boom;

    class HttpResponse implements ResponseInterface
    {
        public $statusCode = 200;
        public $statusText;


        /**
         * @var HeaderBag $headers
         */
        public $headers = null;

        public $responseContent = '';

        public $status = '';

        public $cookies = null;


        protected $sessionCookieParameters;
        protected $cookiesToSend = [];


        public function __construct(HeaderBag $headerBag)
        {
            $this->headers = $headerBag;
        }


        public function setFromHTTPResponseCode(int $HTTPResponseCode)
        {
            $this->statusCode = $HTTPResponseCode;
            $this->statusText = HTTPResponseCode::$statusTexts[$HTTPResponseCode];
        }


        public function header($name, $value)
        {
            $this->headers->set($name, $value);
            return $this;
        }


        public function addHeaders($headers)
        {
            if ($headers instanceof HeaderBag) {
                $headers = $headers->get();
            }

            // keep the previously set headers
            foreach ($headers as $name => $header) {
                $this->headers->set($name, $header);
            }

            return $this;
        }

        public function sendFile($file)
        {
        }


        public function downloadFile($file)
        {
        }


        public function init()
        {
            // TODO: Implement init() method.
        }

        public function makeResponse()
        {
            $content = $this->responseContent;

            if ($content instanceof Boom) {
                // check if boom has any headers, and add them to the response
                $this->headers->add($content->getHeaders());
                $this->statusCode = $content->statusCode;
                return $content;
            }

            if ($content instanceof Renderable) {
                return $content->getOutput();
            }

            if (is_array($content)) {
                return json_encode($content, JSON_UNESCAPED_UNICODE);
            }

            if (is_string($content) || is_numeric($content)) {
                return "" . $content;
            }
        }

        public function setCookieBag($cookieBag)
        {
            $this->cookies = $cookieBag;
        }

        public function setSessionCookieParams($params)
        {
            $this->sessionCookieParameters = [
                'COOKIE' => $params['COOKIE'],
                'LIFETIME' => ($params['LIFETIME'] * 60),
                'PATH' => $params['PATH'],
                'DOMAIN' => $params['DOMAIN'],
                'SECURE' => $params['SECURE'] ? true : false,
                'HTTP_ONLY' => $params['HTTP_ONLY'] ? true : false,
            ];
        }

        public function setSessionCookie($id)
        {
            $this->setCookie($this->sessionCookieParameters['COOKIE'],
                $id,
                time() + (int)$this->sessionCookieParameters['LIFETIME'],
                $this->sessionCookieParameters['PATH'],
                $this->sessionCookieParameters['DOMAIN'],
                $this->sessionCookieParameters['SECURE'],
                $this->sessionCookieParameters['HTTP_ONLY'],);
        }


        /**
         * sets a cookie for the next request
         *
         * @param       $key
         * @param       $value
         * @param null  $expires
         * @param null  $path
         * @param null  $domain
         * @param false $secure
         * @param false $httpOnly
         */
        public function setCookie(
            $key,
            $value,
            $expires = null,
            $path = null,
            $domain = null,
            $secure = false,
            $httpOnly = false
        ) {
            $this->cookiesToSend[$key] = [
                'value' => $value,
                'parameters' => compact('expires', 'path', 'domain', 'secure', 'httpOnly')
            ];
        }


        public function deleteCookie(string $key)
        {
            $this->cookiesToSend[$key] = false;
        }


        public function sendCookies()
        {
            $this->cookies->sendQueuedCookies($this->cookiesToSend);
        }
    }

