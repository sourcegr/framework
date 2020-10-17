<?php


    namespace Sourcegr\Framework\Http;


    use Sourcegr\Framework\Http\Response\HTTPResponseCodes;

    class Boom
    {
        public $statusCode = 500;
        public $payload;
        public $message = '';

        public function __construct($statusCode = HTTPResponseCodes::HTTP_OK)
        {
            $this->setStatusCode($statusCode);
        }

        public function send404() {
            $this->statusCode = 404;
            return $this;
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
            $this->setMessage(HTTPResponseCodes::$statusTexts[$statusCode]);
            return $this;
        }
    }