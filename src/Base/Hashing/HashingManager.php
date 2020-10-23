<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Hashing;


    use Sourcegr\Framework\Base\GenericManager;
    use Sourcegr\Framework\Base\Hashing\Engine\HasherInterface;


    class HashingManager extends GenericManager implements HashingManagerInterface
    {
        public function createHasher(string $hasherName, array $hasherConfig): HasherInterface
        {
            $hasherClass = __NAMESPACE__ . "\\Engine\\" . ucfirst(strtolower($hasherName).'Hasher');
            $hasher = new $hasherClass($hasherConfig);
            $this->add($hasherName, $hasher);
            return $hasher;
        }

        public function attachHasher(string $hasherName, HasherInterface $hasher): HasherInterface
        {
            $this->add($hasherName, $hasher);
            return $hasher;
        }


        public function hasher(string $hasherName): ?HasherInterface
        {
            $hasher = $this->get($hasherName) ?? null;

            if ($hasher === null) {
                throw new \Exception('HashingManager: Hasher does not exist');
            }
            return $hasher;
        }
    }