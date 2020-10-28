<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Session;


    use Sourcegr\Framework\Base\Encryptor\EncryptorInterface;
    use Sourcegr\Framework\Base\Helpers\Str;
    use Sourcegr\Framework\Base\ParameterBag;
    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\BoomException;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;


    class SessionBag extends ParameterBag implements SessionInterface
    {
        const PREVIOUS_URL_NAME = '__previous.url';
        const FLASH = '__FLASH';
        const OLD_FLASH_KEY = 'OLD';
        const NEW_FLASH_KEY = 'NEW';

        public static $tokenName = '__token';
        public static $userIdName = '__user_id';
        public $id;

        protected $engine;


        protected $name;
        protected $encryptor = null;

        protected $isStarted = false;

        protected $originalSession = '';


        public function __construct($engine, $tokenName = '__token', $userIdName = '__user_id', array $parameters = [])
        {
            $this->engine = $engine;
            parent::__construct($parameters);

            static::$tokenName = $tokenName ?? '__token';
            static::$userIdName = $userIdName ?? '__user_id';
            $this->set(static::$userIdName, null);

            $this->regenerateToken();
            $this->setFreshFlash();
        }

        protected function getFromEngine()
        {
            $data = $this->engine->loadData($this->id);
            if (!$data) {
                return [];
            }

            $this->originalSession = $this->decryptSessionVars($data);
            $parsed = json_decode($this->originalSession, true);

            if ($parsed === null) {
                throw new BoomException(new Boom(HTTPResponseCode::HTTP_UNPROCESSABLE_ENTITY),
                    'DBSessionEngine: Cannot decode session data');
            }

            return $parsed;
        }

        protected function prepareForEngine()
        {
            $jsonData = json_encode($this->all());
            if ($jsonData === $this->originalSession) {
                //save some cpu
                return null;
            }

            if ($jsonData === false) {
                throw new BoomException(new Boom(HTTPResponseCode::HTTP_UNPROCESSABLE_ENTITY),
                    'DBSessionEngine: Cannot encode session data');
            }

            if ($this->encryptor) {
                return $this->encryptor->encrypt($jsonData);
            }

            return $jsonData;
        }

        protected function decryptSessionVars($data)
        {
            if ($this->encryptor) {
                return $this->encryptor->decrypt($data);
            }

            return $data;
        }

        public function setEncryptorEngine(EncryptorInterface $encryptor = null)
        {
            if ($encryptor === null) {
                $this->encryptor = null;
            } else {
                $this->encryptor = $encryptor;
            }
        }


        public function getUserIdField()
        {
            return $this->get(static::$userIdName);
        }

        public function setTokenName(string $tokenName)
        {
            static::$tokenName = $tokenName;
            return $this;
        }

        public function getToken(): string
        {
            return $this->get(static::$tokenName);
        }

        public function regenerateToken()
        {
            $this->set(static::$tokenName, Str::random(40));
            return $this;
        }

        public function getTokenName()
        {
            return static::$tokenName;
        }


        public function setId($id)
        {
            $this->id = $id;
            return $this;
        }


        public function start()
        {
            $this->loadSession();
            if (!$this->has(static::$tokenName)) {
                $this->regenerateToken();
            }

            $this->isStarted = true;
            return $this;
        }

        public function loadSession()
        {
            $parsed = $this->getFromEngine();

            if ($parsed) {
                $this->add($parsed);
            }
            return $this;
        }


        public function getPreviousURL()
        {
            return $this->get(self::PREVIOUS_URL_NAME);
        }

        public function setPreviousUrl($url)
        {
            $this->set(self::PREVIOUS_URL_NAME, $url);
            return $this;
        }


        public function all()
        {
            return $this->parameters;
        }

        public function forget($keys)
        {
            $this->remove($keys);
            return $this;
        }

        public function flush()
        {
            $this->clear();
            return $this;
        }


        public function expireFlashData()
        {
            $flash = $this->get(static::FLASH);
            $flash[static::OLD_FLASH_KEY] = $flash[static::NEW_FLASH_KEY];
            $flash[static::NEW_FLASH_KEY] = [];
            $this->set(static::FLASH, $flash);
        }

        public function getFlash($name = null)
        {
            $flash = $this->get(static::FLASH);
            if ($name) {
                return $flash[static::OLD_FLASH_KEY][$name] ?? null;
            } else {
                return $flash[static::OLD_FLASH_KEY];
            }
        }


        public function setFlash($name, $value)
        {
            $flash = $this->get(static::FLASH);
            $flash[static::NEW_FLASH_KEY][$name] = $value;
            $this->set(static::FLASH, $flash);
            return $this;
        }

        public function addFlash($name, $value)
        {
            $flash = $this->get(static::FLASH);
            $flash[static::NEW_FLASH_KEY][$name] = $value;
            $this->set(static::FLASH, $flash);
            return $this;
        }

        public function setFreshFlash()
        {
            $this->set(static::FLASH,
                [
                    static::NEW_FLASH_KEY => [],
                    static::OLD_FLASH_KEY => [],
                ]);

            return $this;
        }

        public function persist()
        {
            $data = $this->prepareForEngine();
            if ($data !== null) {
                $this->engine->persist($this->id, $data);
            }
        }
    }


    //

    //
    //
    //        public function save()
    //        {
    //            $this->handleFlashData();
    //            $this->engine->save(
    //                $this->getId(),
    //                serialize(Arr::ensureArray($this->parameters))
    //            );
    //            $this->isStarted = false;
    //
    //            return $this;
    //        }
    //
    //        public function handleFlashData () {
    //
    //        }
    //
    //
    //        public function pull($key, $default = null)
    //        {
    //            if ($this->has($key)) {
    //                $val = $this->get($key);
    //                $this->forget($key);
    //                return $val;
    //            } else {
    //                return $default;
    //            }
    //        }
    //
    //
    //
    //        public function put(string $key, $value = null): SessionBag
    //        {
    //            if (!Arr::isArray($key)) {
    //                $key = [$key => $value];
    //            }
    //            $this->add($key);
    //            return $this;
    //        }
    //
    //
    //

    //        public function invalidate()
    //        {
    //            $this->flush();
    //            $this->migrate();
    //            // TODO: Implement migrate.
    //            return $this;
    //        }
    //
    //
    //        public function regenerate($destroy = false)
    //        {
    //            $this->migrate($destroy);
    //            $this->regenerateToken();
    //        }
    //
    //
    //        public function migrate($destroy = false)
    //        {
    //            if ($destroy) {
    //                $this->engine->destroy($this->getId());
    //            }
    //            $this->setId(Str::random(40));
    //
    //            return true;
    //        }
    //
    //
    //        public function isStarted()
    //        {
    //            return $this->isStarted;
    //        }
    //
    //

    //
    //
    //        public function getEngine()
    //        {
    //            return $this->engine;
    //        }
    //    }