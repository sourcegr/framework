<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Request;


    use Sourcegr\Framework\Base\Encryptor\DecryptionException;
    use Sourcegr\Framework\Base\Encryptor\EncryptorInterface;
    use Sourcegr\Framework\Base\ParameterBag;

    class COOKIEParameterBag extends ParameterBag implements CookieInterface
    {
        protected $toSend = [];


        /**
         * @var EncryptorInterface $encryptor
         */
        protected $encryptor = null;


        protected function encryptCookie($cookieName, $cookieValue)
        {
            if ($this->encryptor) {
                return $this->encryptor->encrypt($cookieValue);
            }
            return $cookieValue;
        }


        public function setEncryptorEngine(EncryptorInterface $encryptor = null)
        {
            if ($encryptor === null) {
                $this->encryptor = null;
            } else {
                $this->encryptor = $encryptor;
            }
        }

        /**
         * retrieves a coookie value
         *
         * @param null $key
         * @param null $default
         *
         * @return array|mixed|null
         */
        public function get($key = null, $default = null)
        {
            $val = parent::get($key, $default);

            if (!$val) {
                return $default;
            }


            if ($this->encryptor) {
                try {
                    return $this->encryptor->decrypt($val);
                } catch (DecryptionException $e) {
                    return $default;
                }
            }

            return $val;
        }

        public function sendQueuedCookies($cookies)
        {
            foreach ($cookies as $cookieName => $params) {
                if ($params === false) {
//                  $params = false means delete the cookie
                    setcookie($cookieName, null, -2628000);
                    continue;
                }

                setcookie($cookieName, $this->encryptCookie($cookieName, $params['value']), $params['parameters']);
//                setcookie($cookieName, $params['value'], $params['parameters']);
            }
        }
    }