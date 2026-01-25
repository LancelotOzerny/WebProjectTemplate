<?php

namespace Modules\Orm;

use Modules\Database\Connection;
use Modules\Database\IQueryBuilder;
use Modules\Database\PdoQueryBuilder;

class Repository
{
    protected Entity $model;
    protected \PDO $pdo;
    protected IQueryBuilder $queryBuilder;

    public function __construct(Entity $model)
    {
        $this->model = $model;
        $this->pdo = Connection::getPdo();
        $this->queryBuilder = new PdoQueryBuilder();
    }

    public function findAll() : ?array
    {
        $this->queryBuilder->select(['*'])
            ->from($this->model->getTableName());

        $stmp = $this->executeQuery($this->queryBuilder);
        $rows = $stmp->fetchAll(\PDO::FETCH_ASSOC);
        $models = [];

        foreach ($rows as $row)
        {
            $model = clone $this->model;

            foreach ($row as $key => $value)
            {
                $model->$key = $value;
            }

            $model->id = (int)$row['id'] ?? null;
            $models[] = $model;
        }

        return $models;
    }
    public function findById(int $id) : ?Entity
    {
        $this->queryBuilder
            ->select(['*'])
            ->from($this->model->getTableName())
            ->where('id = ?', [$id]);

        $stmp = $this->executeQuery($this->queryBuilder);
        $row = $stmp->fetch(\PDO::FETCH_ASSOC);

        if ($row)
        {
            foreach ($row as $key => $value)
            {
                $this->model->$key = $value;
            }
            $this->model->id = (int)$row['id'] ?? null;

            return $this->model;
        }

        return null;
    }

    public function deleteById(int $id) : bool
    {
        return true;
    }



    protected function executeQuery(IQueryBuilder $builder): \PDOStatement
    {
        $sql = $builder->getSql();
        $params = $builder->getParams();

        try
        {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }
        catch (\PDOException $e)
        {
            error_log('Query failed: ' . $e->getMessage());
            throw $e;
        }
    }
}