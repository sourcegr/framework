<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\View;


    interface Renderable
    {
        public function render(...$params);
        public function getOutput(...$params): string;
    }