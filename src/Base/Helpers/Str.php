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
            $str = str_replace(['/', '+', '='], '', base64_encode(random_bytes($size*2)));
            return substr($str, 0, $size);
        }
    }