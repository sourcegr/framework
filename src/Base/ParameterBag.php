<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base;


    use Countable;
    use IteratorAggregate;
    use Sourcegr\Framework\Base\Helpers\Arr;

    class ParameterBag implements IteratorAggregate, Countable
    {
        protected $parameters;

        public function __construct(array $parameters = [])
        {
            $this->parameters = Arr::ensureArray($parameters);
        }


        public function clear()
        {
            $this->parameters = [];
            return $this;
        }

        public function keys()
        {
            return Arr::keys($this->parameters);
        }

        public function values()
        {
            return Arr::values($this->parameters);
        }

        /**
         * @param string $key
         *
         * @return mixed
         */
        public function has(string $key)
        {
            return isset($this->parameters[$key]);
        }


        /**
         * @param      $parametersOrKey
         * @param null $value
         *
         * @return $this
         */
        public function add($parametersOrKey, $value = null): ParameterBag
        {
            if (!Arr::is($parametersOrKey)) {
                $parametersOrKey = [$parametersOrKey => $value];
            }

            $this->parameters = Arr::arrayReplace($this->parameters, $parametersOrKey);

            return $this;
        }

        /**
         * @param      $key
         * @param null $value
         *
         * @return $this
         */
        public function setIfExists($key, $value)
        {
            if ($this->has($key)) {
                $this->set($key, $value);
            }
            return $this;
        }

        /**
         * removes key oor keys
         *
         * @param mixed $key key or array of keys to remove
         *
         * @return ParameterBag
         */
        public function remove($key): ParameterBag
        {
            $arr = Arr::ensureArray($key);
            foreach ($arr as $arrayKey) {
                unset($this->parameters[$arrayKey]);
            }
            return $this;
        }

        public function set($key, $value)
        {
            $this->parameters[$key] = $value;
            return $this;
        }

        public function get($key = null, $default=null)
        {
            if (!$key) {
                return $this->parameters;
            }
            $ret = $this->parameters[$key] ?? $default;
            return $ret;
        }

        public function count()
        {
            return \count($this->parameters);
        }

        public function getIterator()
        {
            return new \ArrayIterator($this->parameters);
        }
    }