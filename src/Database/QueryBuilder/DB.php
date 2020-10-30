<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database\QueryBuilder;




    use Sourcegr\Framework\Database\GrammarInterface;
    use Sourcegr\Framework\Database\PDOConnection\PDOConnection;

    class DB implements DBInterface
    {
        protected $config = null;
        protected $grammar;


        public function __construct(PDOConnection $PDOConnection)

        {
            $this->grammar = $PDOConnection->grammar;
        }


        /**
         * @param $table
         *
         * @return QueryBuilder
         */
        public function Table(string $table): QueryBuilder
        {
            $qb = new QueryBuilder($this->grammar, $table);
            return $qb;
        }


        public function getGrammar(): GrammarInterface
        {
            return $this->grammar;
        }


        public function setGrammar(GrammarInterface $grammar): bool
        {
            if ($grammar instanceof GrammarInterface) {
                $this->grammar = $grammar;
                return true;
            }
            return false;
        }
    }