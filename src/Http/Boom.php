<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http;


    use JsonSerializable;
    use Sourcegr\Framework\Base\ParameterBag;
    use Sourcegr\Framework\Http\Response\HeaderBag;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;

    class Boom implements JsonSerializable
    {
        public $statusCode = 500;
        public $payload;
        public $message = '';

        public $headers;
        public $flash;

        protected $halt = false;

        public function __construct($statusCode = HTTPResponseCode::HTTP_OK, $message='', $payload = null, $haltsExecution = false)
        {
            $this->setStatusCode($statusCode);
            $this->message = $message;
            $this->payload = $payload;
            $this->headers = new HeaderBag();
            $this->halt = $haltsExecution;
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
            if ($this->message === '') {
                $this->message = HTTPResponseCode::$statusTexts[$statusCode];
            }
            return $this;
        }


        public function haltsExecution():bool {
            return $this->halt;
        }

        public function jsonSerialize()
        {
            return [
                'code' => $this->statusCode,
                'message' => $this->message,
                'payload' => $this->payload
            ];
        }
    }