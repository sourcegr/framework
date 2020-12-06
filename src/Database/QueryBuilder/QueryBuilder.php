<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Database\QueryBuilder;


    use InvalidArgumentException;
    use Sourcegr\Framework\Database\GrammarInterface;
    use Sourcegr\Framework\Database\QueryBuilder\Exceptions\UpdateErrorException;

    class QueryBuilder
    {
        private $debug = false;
        private $table;
        private $params;

        private $cols = '*';
        private $selectAll = true;
        private $wheres = [];

        private $orderBy = null;

        private $rowCountStartAt = 0;
        private $rowCountLimit = null;

        private $joins = [];
        private $groupBy = null;
        private $having = null;

        private $sqlParams = [];

        private $grammar;
        private $returning = null;


        public function __construct(GrammarInterface $grammar, string $table)
        {
            $this->table = $table;
            $this->grammar = $grammar;
            $this->params = new Params($this);
        }

        public function setDebug($debug = true){
            $this->debug = $debug;
            return $this;
        }

        public function setGrammar($grammar)
        {
            $this->grammar = $grammar;
            return $this;
        }

        public function getGrammar()
        {
            return $this->grammar;
        }

        public function getTable()
        {
            return $this->table;
        }


        public function cols()
        {
            $args = func_get_args();
            return $this->columns(...$args);
        }

        public function returning()
        {
            $args = func_get_args();
            $count = count($args);

            if ($count == 0) { // ()
                $this->returning = '*';
                return $this;
            }

            if ($count == 1) {
                $returning = $args[0];

                if (is_array($returning)) { // (['id']), (['id', 'lala'])
                    if (count($returning) == 0) {
                        $this->returning = '*';
                        return $this;
                    }

                    $this->returning = $returning;
                    return $this;
                }

                if ($returning === '' || $returning === '*') { // (''), ('*'),
                    $this->returning = '*';
                    return $this;
                }


                $this->returning = array_map('trim', explode(',', $returning));
                return $this;
            }

            $this->returning = $args;
            return $this;
        }

        public function columns()
        {
            $this->selectAll = false;
            $args = func_get_args();
            $count = count($args);

            if ($count == 0) { // ()
                $this->selectAll = true;
                $this->cols = null;
                return $this;
            }

            if ($count == 1) {
                $cols = $args[0];

                if (is_array($cols)) { // (['id']), (['id', 'lala'])
                    if (count($cols) == 0) {
                        $this->selectAll = true;
                        $this->cols = null;
                        return $this;
                    }
                    $this->cols = $cols;
                    return $this;
                }

                if ($cols === '' || $cols === '*') { // (''), ('*'),
                    $this->selectAll = true;
                    $this->cols = null;
                    return $this;
                }


                $this->cols = array_map('trim', explode(',', $cols));
                return $this;
            }
            $this->cols = $args;
            return $this;
        }

        public function offset($rowsToSkip)
        {
            $this->rowCountStartAt = $rowsToSkip;
            return $this;
        }

        public function limit($rowCount)
        {
            $this->rowCountLimit = $rowCount;
            return $this;
        }

        public function orderBy($orderDefinition)
        {
            $this->orderBy = $orderDefinition;
            return $this;
        }

        public function groupBy($groupByDefinition)
        {
            $this->groupBy = $groupByDefinition;
            return $this;
        }

        public function having()
        {
            $args = func_get_args();
            [$col, $eq, $val] = array_push($args, null, null);

            if ($eq === null) {
                $eq = '';
                $val = '';
            }

            if ($val === null) {
                [$val, $eq] = [$eq, '='];
            }
            $this->having = [$col, $eq, $val];
            return $this;
        }


        public function where($col, $mod = null, $val = null)
        {
            return $this->W('AND', $col, $mod, $val);
        }

        public function orWhere($col, $mod = null, $val = null)
        {
            return $this->W('OR', $col, $mod, $val);
        }

        public function whereIn($col, $arr = [])
        {
            return $this->W('AND', $col, 'IN', $arr);
        }

        public function orWhereIn($col, $arr = [])
        {
            return $this->W('OR', $col, 'IN', $arr);
        }

        public function whereLike($col, $arr = [])
        {
            return $this->W('AND', $col, 'LIKE', $arr);
        }

        public function orWhereLike($col, $arr = [])
        {
            return $this->W('OR', $col, 'LIKE', $arr);
        }

        public function whereNotLike($col, $arr = [])
        {
            return $this->W('AND', $col, 'NOT LIKE', $arr);
        }

        public function orWhereNotLike($col, $arr = [])
        {
            return $this->W('OR', $col, 'NOT LIKE', $arr);
        }

        public function whereNotIn($col, $arr = [])
        {
            return $this->W('AND', $col, 'NOT IN', $arr);
        }

        public function orWhereNotIn($col, $arr = [])
        {
            return $this->W('OR', $col, 'NOT IN', $arr);
        }

        public function whereNull($col)
        {
            return $this->W('AND', $col, null, 'IS NULL');
        }

        public function orWhereNull($col)
        {
            return $this->W('OR', $col, null, 'IS NULL');
        }

        public function whereNotNull($col)
        {
            return $this->W('AND', $col, null, 'IS NOT NULL');
        }

        public function orWhereNotNull($col)
        {
            return $this->W('OR', $col, null, 'IS NOT NULL');
        }


        public function join($table, $joinText, $joinType = 'INNER')
        {
            $this->joins[] = "$joinType JOIN $table ON $joinText";
            return $this;
        }

        public function leftJoin($table, $joinText)
        {
            $this->joins[] = "LEFT JOIN $table ON $joinText";
            return $this;
        }

        public function rightJoin($table, $joinText)
        {
            $this->joins[] = "RIGHT JOIN $table ON $joinText";
            return $this;
        }

        public function createSQLLimit()
        {
            return $this->grammar->createLimit($this->rowCountLimit, $this->rowCountStartAt);
        }

        public function createSelect()
        {
            $this->sqlParams = [];
            $notNullAdder = new NotNullAdder();

            [$whereParams, $whereString] = $this->params->createSQLWhere();
            array_push($this->sqlParams, ...$whereParams);

            $notNullAdder->addNotNull('SELECT',
                $this->selectAll ? '*' : implode(',', $this->cols),
                'FROM',
                $this->table,
                (count($this->joins) > 0) ? implode(' ', $this->joins) : null,
                $whereString,
                $this->groupBy ? 'GROUP BY ' . $this->groupBy : null,
                ($this->groupBy && $this->having) ? 'HAVING ' . $this->having : null,
                $this->orderBy ? 'ORDER BY ' . $this->orderBy : null,
                $this->createSQLLimit());

            return [$notNullAdder->join(' '), $this->sqlParams];
        }


        public function max($col)
        {
            return $this->select("MAX($col) as max")[0]['max'] ?? false;
        }

        public function min($col)
        {
            return $this->select("MIN($col) AS min")[0]['min'] ?? false;
        }

        public function groupCount($groupCol)
        {
            $final = [];
            $res = $this->groupBy("$groupCol")->select("$groupCol, COUNT(*) as count");
            foreach ($res as $result) {
                $final[$result[$groupCol]] = $result['count'];
            }
            return $final;
        }

        public function count()
        {
            return $this->select("COUNT(*) AS count")[0]['count'] ?? false;
        }

        public function find($id, $idName = 'id')
        {
            return $this->where($idName, $id)->first();
        }

        public function first($fields = null)
        {
            $res = $this->select($fields);
            if (is_array($res)) {
                return $res[0] ?? null;
            }
            return null;
        }


        public function select($fields = null)
        {
            if ($fields !== null) {
                $this->cols($fields);
            }

            [$sql, $params] = $this->createSelect();
            if ($this->debug) {
                dd($sql, $params);
            }
            return $this->grammar->select($sql, $params);
        }

        public function update()
        {
            $this->sqlParams = [];
            $args = func_get_args();
            $argsLength = count($args);

            if ($argsLength == 1) {
                $updateDefinition = $args[0];
            } elseif ($argsLength == 2) {
                $updateDefinition = [];
                $updateDefinition[$args[0]] = $args[1];
            } else {
                $updateDefinition = null;
            }

            if (!is_array($updateDefinition)) {
                throw new InvalidArgumentException('The parameter for the update function should be an associative array or exactly two parameters (column to update, value)');
            }

            if (count($updateDefinition) == 0) {
                return 0;
            }

            if ($this->rowCountLimit || $this->rowCountStartAt) {
                throw new UpdateErrorException('LIMIT/OFFSET cannot be used with UPDATE');
            }

            $PLACEHOLDER = [$this->grammar, 'getPlaceholder'];

            $insertStringParts = [];

            foreach ($updateDefinition as $col => $value) {
                if (is_numeric($col)) {
                    throw new InvalidArgumentException('Column name should be string');
                }

                if ($value instanceof Raw) {
                    $insertStringParts[] = "$col = " . $value->getValue();
                } else {
                    $insertStringParts[] = "$col = " . $PLACEHOLDER();
                    $this->sqlParams[] = $value;
                }
            }

            $sql = implode(',', $insertStringParts);

            $notNullAdder = new NotNullAdder();

            [$whereParams, $whereString] = $this->params->createSQLWhere();
            array_push($this->sqlParams, ...$whereParams);
            $notNullAdder->addNotNull('UPDATE',
                $this->table,
                'SET',
                $sql,
                $whereString);

            $sql = $notNullAdder->join(' ');
            if ($this->debug) {
                dd($sql, $this->sqlParams);
            }
            return $this->grammar->update($sql, $this->sqlParams, $this->returning);
        }

        public function insert($insertDefinition = null)
        {
            $this->sqlParams = [];
            if (!is_array($insertDefinition) || !count($insertDefinition)) {
                throw new InvalidArgumentException('The parameter for the insert function should be a non-empty associative array');
            }

            $PLACEHOLDER = [$this->grammar, 'getPlaceholder'];

            $columns = [];
            $placeholders = [];

            foreach ($insertDefinition as $col => $value) {
                if (is_numeric($col)) {
                    throw new InvalidArgumentException('Only associative array is allowed as parameter for the insert function');
                }

                $columns[] = $col;

                if ($value instanceof Raw) {
                    $placeholders[] = $value->getValue();
                } else {
                    $placeholders[] = $PLACEHOLDER();
                    $this->sqlParams[] = $value;
                }
            }

            $columnsText = implode(',', $columns);
            $placeholdersText = implode(',', $placeholders);

            $sql = 'INSERT INTO ' . $this->table . " ($columnsText) VALUES ($placeholdersText)";

            if ($this->debug) {
                dd($sql, $this->sqlParams);
            }
            return $this->grammar->insert($sql, $this->sqlParams, $this->returning);
        }

        public function delete()
        {
            $this->sqlParams = [];
            $notNullAdder = new NotNullAdder();

            [$whereParams, $whereString] = $this->params->createSQLWhere();
            array_push($this->sqlParams, ...$whereParams);

            $notNullAdder->addNotNull('DELETE FROM',
                $this->table,
                $whereString);
            $sql = $notNullAdder->join(' ');

            if ($this->debug) {
                dd($sql, $this->sqlParams);
            }
            return $this->grammar->delete($sql, $this->sqlParams, $this->returning);
        }


        private function W($term, $col, $mod, $val)
        {
            if (is_callable($col)) {
                $subQuery = new static($this->grammar, $this->table);
                $col($subQuery);
                $subQuery->params->data['term'] = count($this->params->data['data']) ? $term : '';
                $this->params->data['data'][] = $subQuery->params;
                return $this;
            }

            $this->params->parse_input_clause($term, $col, $mod, $val);
            return $this;
        }

        public function getCols()
        {
            return $this->cols;
        }
    }
