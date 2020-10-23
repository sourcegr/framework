<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Redirect;


    use Sourcegr\Framework\Http\Response\HTTPResponseCode;


    class PermanentRedirect extends Redirect
    {
        public $statusCode = HTTPResponseCode::HTTP_PERMANENTLY_REDIRECT;
    }