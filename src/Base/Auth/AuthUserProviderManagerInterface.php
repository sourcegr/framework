<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth;


    use Sourcegr\Framework\Base\Auth\Engine\AuthUserProviderInterface;

    interface AuthUserProviderManagerInterface
    {

        public function createProvider(string $providerName, array $providerConfig): AuthUserProviderInterface;

        public function attachProvider(string $driveName, AuthUserProviderInterface $drive): AuthUserProviderInterface;

        public function provider(string $providerName): ?AuthUserProviderInterface;

        public function all(): array;
    }