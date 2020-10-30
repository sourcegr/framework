<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database\Freakquent;


    use Sourcegr\Framework\Database\QueryBuilder\QueryBuilder;


    class Freakquent
    {
        private static $queryBuilder = null;

        public function __construct(QueryBuilder $queryBuilder)
        {
            $this->queryBuilder = $queryBuilder;
        }
    }
