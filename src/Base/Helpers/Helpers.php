<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Base\Helpers;


    class Helpers
    {
        public static function getRequestHeaders($bank = null)
        {
            $headers = [];
            $bank = $bank ?? $_SERVER;

            foreach ($bank as $key => $value) {
                if (substr($key, 0, 5) <> 'HTTP_') {
                    continue;
                }
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
            return $headers;
        }
    }