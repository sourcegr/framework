<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Helpers;


    class Str
    {
        /**
         * returns random string of $size length
         *
         * @param $size
         *
         * @return false|string
         * @throws \Exception
         */
        public static function random($size)
        {
            $str = str_replace(['/', '+', '='], '', base64_encode(random_bytes($size * 2)));
            return substr($str, 0, $size);
        }

        public static function startsWith(string $haystack, string $needle)
        {
            return strncmp($haystack, $needle, strlen($needle)) === 0;
        }

        public static function endsWith(string $haystack, string $needle)
        {
            return $needle !== '' && substr($haystack, -strlen($needle)) === $needle;
        }

        public static function toCamelCase($string, $capitalizeFirstCharacter = false)
        {
            $str = str_replace('-', '', ucwords(str_replace('_', '-', $string), '-'));

            if (!$capitalizeFirstCharacter) {
                $str = lcfirst($str);
            }

            return $str;
        }
    }