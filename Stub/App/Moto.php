<?php


    namespace Sourcegr\Stub\App;


    class Moto implements MotoInterface
    {
        public function __construct(int $wheels = 5)
        {
            $this->wheels = $wheels;
        }
    }