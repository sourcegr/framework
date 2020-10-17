<?php

    declare(strict_types=1);

    namespace Sourcegr\Framework\App\Container;

    use Exception;
    use Psr\Container\NotFoundExceptionInterface;

    class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
    {
        //
    }
