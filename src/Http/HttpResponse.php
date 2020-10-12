<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http;


    use Sourcegr\Framework\Http\Response\HeaderBag;

    class HttpResponse
    {
        public $headers = null;
        public $cookies = [];

        public $textContent = '';
        public $status = '';



        public function __construct(string $textContent, int $status, array $headers = [])
        {
            $this->headers = new HeaderBag($headers);
            $this->status = $status;
            $this->textContent = $textContent;
        }

//        public static function __callStatic($name, $arguments)
//        {
//            $instance = new static();
//            if (method_exists($instance, $name)) {
//                $instance->$name(...$arguments);
//                return $instance;
//            }
//            throw new \Exception('No such method');
//        }

        public function redirect($to, $code=301)
        {

        }

        public function leave($to, $code=301)
        {

        }


        public function goBack()
        {

        }

        public function header($name, $value){
            $this->headers->set($name, $value);
            return $this;
        }


        public function addHeaders($headers){
            if ($headers instanceof HeaderBag) {
                $headers = $headers->get();
            }

            foreach ($headers as $name => $header) {
                $this->headers->set($name, $header);
            }

            return $this;
        }

        public function cookie($name, $value=null, $minutes=0, $path='/', $domain=null, $secure=null, $httpOnly=null){
//            if ($name instanceof Cookie) {
//
//            }
            $this->headers->addCookie($name, [
                'value' => $value,
                'minutes' => $minutes,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httpOnly' => $httpOnly
            ]);
            return $this;
        }

        public function json($data) {
            $this->textContent = json_encode($data);
        }


        public function sendFile($file) {
        }

        public function downloadFile($file) {
        }


    }

