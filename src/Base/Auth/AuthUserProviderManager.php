<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Auth;


    use Sourcegr\Framework\Base\GenericManager;
    use Sourcegr\Framework\Base\Auth\Engine\AuthUserProviderInterface;

    class AuthUserProviderManager extends GenericManager implements AuthUserProviderManagerInterface
    {
        public function createProvider(string $providerName, array $providerConfig): AuthUserProviderInterface
        {
            $providerClass = __NAMESPACE__ . "\\Engine\\" . ucfirst(strtolower($providerConfig['engine']).'AuthUserProvider');
            $provider = new $providerClass($providerConfig);
            $this->add($providerName, $provider);
            return $provider;
        }

        public function attachProvider(string $providerName, AuthUserProviderInterface $provider): AuthUserProviderInterface
        {
            $this->add($providerName, $provider);
            return $provider;
        }


        public function provider(string $providerName): ?AuthUserProviderInterface
        {
            $provider = $this->get($providerName) ?? null;

            if ($provider === null) {
                throw new \Exception('User provider does not exist');
            }
            return $provider;
        }
    }