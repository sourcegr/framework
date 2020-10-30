<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database\QueryBuilder;


    class NotNullAdder
    {
        private $val = [];

        public function join($glue = '')
        {
            return implode($glue, $this->val);
        }

        public function addNotNull()
        {
            $v = func_get_args();
            foreach ($v as $val) {
                if ($val !== null) {
                    $this->val[] = trim($val);
                }
            }
            return $this;
        }

        public static function create($parts, $glue = ' ')
        {
            $a = new static();
            return $a->addNotNull(...$parts)->join(' ');
        }
    }