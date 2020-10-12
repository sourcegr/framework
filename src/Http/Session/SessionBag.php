<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Session;


    use Sourcegr\Framework\Base\Helpers\Arr;
    use Sourcegr\Framework\Base\Helpers\Str;
    use Sourcegr\Framework\Base\ParameterBag;

    class SessionBag extends ParameterBag
    {
        const TOKEN_NAME = '__token';
        const PREVIOUS_URL_NAME = '__previous.url';

        protected $handler;

        protected $id;
        protected $name;

        protected $isStarted = false;

        /**
         * @return array|mixed
         */
        protected function readFromHandler()
        {
            if ($data = $this->handler->load($this->getId())) {
            $data = @unserialize($data);

            if ($data !== false && ! is_null($data) && Arr::isArray($data)) {
                return $data;
            }
        }

        return [];
        }

        /**
         * SessionBag constructor.
         *
         * @param       $handler
         * @param array $parameters
         */
        public function __construct($handler, array $parameters = [])
        {
            $this->handler = $handler;
            parent::__construct($parameters);
        }

        /**
         * @return string $name the name of the session
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param string $name the name of the session
         */
        public function setName($name)
        {
            $this->name = $name;
        }

        /**
         * @return string $id the ID of the session
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param string $id the ID of the session
         */
        public function setId($id)
        {
            $this->id = $id;
        }

        public function loadSession() {
            $this->parameters = $this->add($this->readFromHandler());
        }

        /**
         * starts the session
         *
         * @return bool
         * @throws \Exception
         */
        public function start()
        {
            $this->loadSession();
            if (!$this->has(self::TOKEN_NAME)) {
                $this->regenerateToken();
            }
            return $this->isStarted = true;
        }

        /**
         * saves the session using the handler
         *
         * @return $this
         */
        public function save()
        {
            $this->handleFlashData();
            $this->handler->save(
                $this->getId(),
                serialize(Arr::ensureArray($this->parameters))
            );
            $this->isStarted = false;

            return $this;
        }

        public function handleFlashData () {

        }

        /**
         * returns all the session data
         *
         * @return $this
         */
        public function all()
        {
            return $this;
        }


        /**
         * gets key and deletes it
         *
         * @param      $key
         * @param null $default
         *
         * @return array|mixed|null
         */
        public function pull($key, $default = null)
        {
            if ($this->has($key)) {
                $val = $this->get($key);
                $this->forget($key);
                return $val;
            } else {
                return $default;
            }
        }

        /**
         * @param string $key   the key to add
         * @param null   $value the value to set
         *
         * @return $this
         */
        public function put(string $key, $value = null): SessionBag
        {
            if (!Arr::isArray($key)) {
                $key = [$key => $value];
            }
            $this->add($key);
            return $this;
        }

        /**
         * gets the token
         *
         * @return string
         */
        public function token(): string
        {
            return $this->get(self::TOKEN_NAME);
        }


        /**
         * regenerates the token
         *
         * @return $this
         * @throws \Exception
         */
        public function regenerateToken(): SessionBag
        {
            $this->set(self::TOKEN_NAME, Str::random(40));
            return $this;
        }

        /**
         * removes the key
         *
         * @param $keys
         *
         * @return $this
         */
        public function forget($keys)
        {
            $this->remove($keys);
            return $this;
        }

        /**
         * clears the bag
         *
         * @return $this
         */
        public function flush()
        {
            $this->clear();
            return $this;
        }

        /**
         * resets the session and restarts it
         *
         * @return $this
         */
        public function invalidate()
        {
            $this->flush();
            $this->migrate();
            // TODO: Implement migrate.
            return $this;
        }

        /**
         * regenerates session
         *
         * @param false $destroy
         */
        public function regenerate($destroy = false)
        {
            $this->migrate($destroy);
            $this->regenerateToken();
        }

        /**
         * Generate a new ID for the session.
         *
         * @param false $destroy whether to destroy
         *
         * @return bool
         * @throws \Exception
         */
        public function migrate($destroy = false)
        {
            if ($destroy) {
                $this->handler->destroy($this->getId());
            }
            $this->setId(Str::random(40));

            return true;
        }

        /**
         * @return bool
         */
        public function isStarted()
        {
            return $this->isStarted;
        }

        /**
         * get the URL of the previous page
         *
         * @return array|mixed|null
         */
        public function previousURL()
        {
            return $this->get(self::PREVIOUS_URL_NAME);
        }

        /**
         * set the URL of the previous page
         *
         * @param $url
         *
         * @return SessionBag
         */
        public function setPreviousUrl($url)
        {
            return $this->set(self::PREVIOUS_URL_NAME, $url);
        }

        /**
         * get the handler
         *
         * @return mixed
         */
        public function getHandler()
        {
            return $this->handler;
        }
    }