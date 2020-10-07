<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base;


    use Sourcegr\Framework\Base\Helpers\Arr;

    class ParameterBag implements \IteratorAggregate, \Countable
    {
        protected $parameters;

        public function __construct(array $parameters = [])
        {
            $this->parameters = Arr::ensureArray($parameters);
        }


        public function clear() {
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
         * @param mixed $parameters
         * @param null  $value
         * @param false $mustExist
         *
         * @return $this
         */
        public function add($parameters, $value = null, $mustExist = false): ParameterBag
        {
            if (!Arr::is($parameters)) {
                $parameters = [$parameters => $value];
            }

            if (!$mustExist || ($mustExist && count($parameters))) {
                $this->parameters = Arr::arrayReplace($this->parameters, $parameters);
            }

            return $this;
        }

        /**
         * @param array $parameters
         * @param null  $value
         *
         * @return $this
         */
        public function addIfExists($parameters = [], $value = null) {
            return $this->add($parameters, $value, true);
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

        public function get($key = null)
        {
            if (!$key) {
                return $this->parameters;
            }
            $ret = $this->parameters[$key] ?? null;
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