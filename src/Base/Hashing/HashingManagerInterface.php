<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Hashing;


    use Sourcegr\Framework\Base\Hashing\Engine\HasherInterface;

    interface HashingManagerInterface
    {

        public function createHasher(string $hasherName, array $hasherConfig): HasherInterface;

        public function attachHasher(string $hasherName, HasherInterface $hasher): HasherInterface;

        public function hasher(string $hasherName): ?HasherInterface;

        public function all(): array;
    }