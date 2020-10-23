<?php

    declare(strict_types=1);


    namespace Sourcegr\Framework\Http\Session;


    use Sourcegr\Framework\Database\PDOConnection\PDOConnection;
    use Sourcegr\Framework\Database\QueryBuilder\QueryBuilder;
    use Sourcegr\Framework\Http\Boom;
    use Sourcegr\Framework\Http\BoomException;
    use Sourcegr\Framework\Http\Response\HTTPResponseCode;

    class DBSessionEngine
    {
        public $PDOConnection;
        public $table;

        public function __construct(PDOConnection $PDOConnection, string $table)
        {
            $this->PDOConnection = $PDOConnection;
            $this->table = $table;
        }

        protected function QB()
        {
            return new QueryBuilder($this->PDOConnection->grammar, $this->table);
        }

        public function loadData($id)
        {
            $data = $this->QB()->where('id', $id)->select('data');
            if (!$data || count($data) === 0) {
                return [];
            }
            if (!$data || count($data) === 2) {
                throw new BoomException(new Boom(HTTPResponseCode::HTTP_INTERNAL_SERVER_ERROR),
                    'DBSessionEngine: Duplicate session id encountered');
            }

            return $data[0]['data'];
        }

        public function persist($id, $data)
        {
            // try update
            $result = $this->QB()->where('id', $id)->update([
                'data' => $data
            ]);


            if (!$result) {
                $result = $this->QB()->insert([
                    'id' => $id,
                    'data' => $data
                ]);
            }

            return $result;
        }

    }