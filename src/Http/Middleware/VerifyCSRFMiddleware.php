<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Middleware;



    use Sourcegr\Framework\Base\Encryptor\EncryptorInterface;
    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\BoomException;
    use Sourcegr\Framework\Http\Request\RequestInterface;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;
    use Sourcegr\Framework\Http\Response\ResponseInterface;

    class VerifyCSRFMiddleware extends BaseMiddleware
    {

    }