<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base;


    class GenericManager extends ParameterBag
    {
        public function all(): array
        {
            return $this->get();
        }
    }