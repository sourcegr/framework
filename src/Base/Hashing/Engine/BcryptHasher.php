<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Hashing\Engine;


    class BcryptHasher implements HasherInterface
    {
        protected $rounds = 10;

        public function __construct($config)
        {
            $this->rounds = $config['rounds'] ?? $this->rounds;
        }


        public function createHash(string $value, array $options = []): string
        {
            $hash = password_hash($value,
                PASSWORD_BCRYPT,
                [
                    'cost' => $options['rounds'] ?? $this->rounds
                ]);

            if ($hash === false) {
                throw new \Exception('BcryptHasher: cannot create hash. Maybe bcrypt hashing is not supported.');
            }

            return $hash;
        }

        public function checkHash(string $value, string $hashed): bool
        {
            return (strlen($hashed) === 0) ? false : password_verify($value, $hashed);
        }
    }