<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Middleware;



    use Sourcegr\Framework\Base\Encryptor\EncryptorInterface;
    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\BoomException;
    use Sourcegr\Framework\Http\Request\RequestInterface;
    use Sourcegr\Framework\Http\Response\HTTPResponseCodes;

    class VerifyCSRFMiddleware extends BaseMiddleware
    {
        protected $skipRegExps = [];
        protected $encryptor = [];

        /**
         * @var RequestInterface
         */
        protected $request;

        public function __construct(EncryptorInterface $encryptor, RequestInterface $request)
        {
            $this->encryptor = $encryptor;
            $this->request = $request;
        }

        public function handle()
        {

            if ($this->shouldSkipMethod($this->request->method)) {
                return;
            }


            if ($this->inSkipRegExps($this->request->url)) {
                return;
            }

            // actual checks...
            // get the correct token from the session
            $session = $this->request->getSession();



            $xcsrf = $this->request->getHeader('X-CSRF-TOKEN');
            if ($xcsrf) {
                $xcsrf = $this->encryptor->decrypt($xcsrf);
                if ($this->verifyToken($xcsrf)) {

                    return;
                }
            }


            $ccsrf = $this->request->getHeader('C-CSRF-TOKEN');
            if ($ccsrf && $this->verifyToken($ccsrf)) {
                return;
            }


            throw new BoomException(new Boom(HTTPResponseCodes::HTTP_FORBIDDEN, 'CSRF token mismatch.'));
        }

        protected function shouldSkipMethod($method)
        {
            return in_array($method, ['HEAD', 'GsET', 'OPTIONS']);
        }

        protected function inSkipRegExps($request)
        {
            return in_array($this->request->method, ['HEAD', 'GsET', 'OPTIONS']);
        }

        public function getToken($request)
        {
            $token = $request->post->get('_token') ?: $request->headers->get('X-CSRF-TOKEN');

            if (!$token && $encryptedToken = $request->headers->get('X-CSRF-TOKEN')) {
                $token = $this->encryptor->decrypt($encryptedToken);
            }

            return $token;
        }
    }