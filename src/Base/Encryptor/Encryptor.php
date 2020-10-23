<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Encryptor;


    use Exception;

    class Encryptor implements EncryptorInterface
    {
        public $key;
        public $cipher;


        public function getKey()
        {
            return $this->key;
        }

        public function addPrefix($to)
        {
            return hash_hmac('sha1', $to . '-appx', $this->key) . '|';
        }

        public function removePrefix($from)
        {
            return substr($from, 41);
        }


        public function __construct($key, $cipher = 'AES-128-CBC')
        {
            $this->key = $key;
            $this->cipher = $cipher;
        }

        public function encrypt($value, $serialize = true)
        {
            $iv = random_bytes(openssl_cipher_iv_length($this->cipher));

            $value = \openssl_encrypt($serialize ? serialize($value) : $value,
                $this->cipher,
                $this->key,
                0,
                $iv);

            if ($value === false) {
                throw new EncryptionException('Encryption Failure (openSSL error)');
            }

            $iv = base64_encode($iv);
            $mac = $this->hash($iv . $value);

            $json = json_encode(compact('iv', 'value', 'mac'), JSON_UNESCAPED_SLASHES);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new EncryptionException('Encryption Failure (payload creation error)');
            }

//            echo "<hr>$json<hr>";
            return base64_encode($json);
        }


        public function encryptString($value)
        {
            return $this->encrypt($value, false);
        }


        public function decrypt($payload, $unserialize = true)
        {
            $payload = $this->getJsonPayload($payload);

            $iv = base64_decode($payload['iv']);


            $decrypted = \openssl_decrypt($payload['value'],
                $this->cipher,
                $this->key,
                0,
                $iv);

            if ($decrypted === false) {
                ss(\openssl_error_string());
                throw new DecryptionException('Decryption Failure (openSSL error)');
            }

            return $unserialize ? unserialize($decrypted) : $decrypted;
        }


        public function decryptString($payload)
        {
            return $this->decrypt($payload, false);
        }


        protected function hash($str)
        {
            return hash_hmac('sha256', $str, $this->key);
        }

        protected function getJsonPayload($payload)
        {
            $payload = json_decode(base64_decode($payload), true);

            $isValid = is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) && strlen(base64_decode($payload['iv'],
                    true)) === openssl_cipher_iv_length($this->cipher);

            if (!$isValid) {
                throw new DecryptionException('Decryption Failure (invalid payload)');
            }

            $isValid = hash_equals($this->hash($payload['iv'] . $payload['value']),
                $payload['mac']);

            if (!$isValid) {
                throw new DecryptionException('Decryption Failure. (invalid hash)');
            }

            return $payload;
        }

    }