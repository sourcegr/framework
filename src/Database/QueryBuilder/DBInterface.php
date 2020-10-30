<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database\QueryBuilder;




    use Sourcegr\Framework\Database\GrammarInterface;
    use Sourcegr\Framework\Database\PDOConnection\PDOConnection;

    interface DBInterface
    {
        public function __construct(PDOConnection $PDOConnection);

        public function Table(string $table): QueryBuilder;

        public function getGrammar(): GrammarInterface;

        public function setGrammar(GrammarInterface $grammar): bool;
    }