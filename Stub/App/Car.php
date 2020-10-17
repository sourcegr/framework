<?php


    namespace Sourcegr\Stub\App;


    class Car implements CarInterface
    {
        public function __construct(MotoInterface $moto, Bike $bike)
        {
            $this->moto = $moto;
            $this->bike = $bike;
        }
    }

