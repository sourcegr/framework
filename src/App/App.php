<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\App;


    use Sourcegr\Framework\Interfaces\App\AppInterface;

    class App implements AppInterface
    {
        public $container = null;

        public function __construct()
        {
            $this->container = new Container();
        }

    }