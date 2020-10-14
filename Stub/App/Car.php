<?php


    namespace Sourcegr\Stub\App;


    class Car implements CarInterface
    {
        public function __construct(MotoInterface $moto)
        {
            $this->moto = $moto;
        }
    }

