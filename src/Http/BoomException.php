<?php

    declare(strict_types=1);

    namespace Sourcegr\Framework\Http;

    use Throwable;

    class BoomException extends \Exception
    {
        public $boom;

        public function __construct(Boom $boom, $message = "", $code = 0, Throwable $previous = null)
        {
            $this->boom = $boom;
            parent::__construct($message, $code, $previous);
        }
    }