<?php


    namespace Sourcegr\Framework\Http;


    class Boom
    {
        public $status_code = 500;

        public function send404() {
            $this->status_code = 404;
            return $this;
        }
    }