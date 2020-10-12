<?php


    namespace Sourcegr\Framework\Base\Helpers;


    class Arr
    {
        public static function ensureArray($var)
        {
            return static::is($var) ? $var : [$var];
        }

        public static function getPureArray($var)
        {
            if (static::isArray($var)) {
                return $var;
            }

            if (static::isArrayObject($var)) {
                return $var->getArrayCopy();
            }

            return null;
        }

        public static function is($var)
        {
            return (\is_array($var) || $var instanceof \ArrayObject);
        }

        public static function isArray($var)
        {
            return \is_array($var);
        }

        public static function isArrayObject($var)
        {
            return $var instanceof \ArrayObject;
        }

        public static function keys($var)
        {
            if (static::isArray($var)) {
                return array_keys($var);
            }

            if (static::isArrayObject($var)) {
                return array_keys($var->getArrayCopy());
            }

            return null;
        }

        public static function values($var)
        {
            if (static::isArray($var)) {
                return array_values($var);
            }

            if (static::isArrayObject($var)) {
                return array_values($var->getArrayCopy());
            }

            return null;
        }

        public static function arrayReplace($init, $with)
        {
            $pureInit = static::getPureArray($init);
            $pureWith = static::getPureArray($with);

            if ($pureWith === null) {
                return $pureInit;
            }
            if ($pureInit === null) {
                return null;
            }

            $combined = \array_replace($pureInit, $pureWith);

            if (static::isArray($init)) {
                return $combined;
            }

            if (static::isArrayObject($init)) {
                $init->exchangeArray($combined);
                return $init;
            }
        }

        public static function merge()
        {
            $all = func_get_args();
            if (count($all) === 1) {
                return static::ensureArray($all[0]);
            }
            foreach ($all as &$anArray) {
                $anArray = self::ensureArray($anArray);
            }

            return array_merge(...$all);
        }
    }