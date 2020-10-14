<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\App;


    class App
    {
        public $container = null;

        public function __construct()
        {
            $this->container = new Container();
        }

    }