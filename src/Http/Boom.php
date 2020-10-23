<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http;


    use Sourcegr\Framework\Base\ParameterBag;
    use Sourcegr\Framework\Http\Response\HeaderBag;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;

    class Boom
    {
        public $statusCode = 500;
        public $payload;
        public $message = '';

        public $headers;
        public $flash;

        public function __construct($statusCode = HTTPResponseCode::HTTP_OK, $payload = null)
        {
            $this->setStatusCode($statusCode);
            $this->payload = $payload;
            $this->headers = new HeaderBag();
            $this->flash = new ParameterBag();
        }

        public function isRedirect()
        {
            return $this->statusCode === HTTPResponseCode::HTTP_TEMPORARY_REDIRECT || $this->statusCode === HTTPResponseCode::HTTP_PERMANENTLY_REDIRECT;
        }

        public function withHeader($headerName, $headerValue)
        {
            $this->headers->set($headerName, $headerValue);
            return $this;
        }


        public function withFlash($flashName, $flashValue)
        {
            $this->flash->set($flashName, $flashValue);
            return $this;
        }


        public function getHeaders()
        {
            return $this->headers->get();
        }

        public function getFlash()
        {
            return $this->headers->get();
        }


        public function setPayload($payload): Boom
        {
            $this->payload = $payload;
            return $this;
        }


        public function setMessage($message): Boom
        {
            $this->message = $message;
            return $this;
        }


        public function setStatusCode(int $statusCode): Boom
        {
            $this->statusCode = $statusCode;
            $this->setMessage(HTTPResponseCode::$statusTexts[$statusCode]);
            return $this;
        }
    }