<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Session;


    use Sourcegr\Framework\App\ContainerInterface;
    use Sourcegr\Framework\Base\Encryptor\EncryptorInterface;
    use Sourcegr\Framework\Base\Helpers\Str;

    class SessionProvider implements SessionProviderInterface
    {
        protected $container;
        protected $sessionBag;
        protected $cookieName = null;

        public $cookieParameters;


        public function __construct(ContainerInterface $container)
        {
            $this->container = $container;
        }

        public function init($config) {
            $this->cookieName = $config['COOKIE'];

            $driver = $config['DRIVER'];
            $conf = $config['DRIVERS'][$driver] ?? null;

            $engine = null;

            if ($driver === 'DB') {
                $connection = $this->container->get('DB.connections.'.$conf['connection']);
                $engine = new DBSessionEngine($connection, $conf['table']);
            }

            $this->sessionBag = new SessionBag($engine, $config['TOKEN_NAME'], $config['USER_ID_FIELD']);

            if ($config['ENCRYPT']) {
                $this->sessionBag->setEncryptorEngine($this->container->get(EncryptorInterface::class));
            }

            return $this->sessionBag;
        }

        public function startSession() {
            $request = $this->container->get('REQUEST');
            $request->session = $this->sessionBag;

            $id = $request->cookie->get($this->cookieName);

            if (!$id) {
                $newId = Str::random(40);
                $request->session->setId($newId);
                $request->session->persist();
                $this->container->get('RESPONSE')->setSessionCookie($newId);
            } else {
                $id = $request->cookie->get($this->cookieName);
                $request->session->setId($id);
                $request->session->start();
            }
        }
    }