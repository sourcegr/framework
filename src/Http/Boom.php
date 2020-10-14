<?php


    namespace Sourcegr\Framework\Http;


    class Boom
    {
        public $statusCode = 500;
        public $payload;

        public function send404() {
            $this->statusCode = 404;
            return $this;
        }

        /**
         * @param mixed $payload
         *
         * @return Boom
         */
        public function setPayload($payload): Boom
        {
            $this->payload = $payload;
            return $this;
        }

        /**
         * @param int $statusCode
         *
         * @return Boom
         */
        public function setStatusCode(int $statusCode): Boom
        {
            $this->statusCode = $statusCode;
            return $this;
        }


    }