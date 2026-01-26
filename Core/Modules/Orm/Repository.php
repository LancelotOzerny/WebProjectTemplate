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

    public function findAll(): ?array
    {
        $this->queryBuilder->select(['*'])
            ->from($this->model->getTableName());

        $stmt = $this->executeQuery($this->queryBuilder);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $models = [];

        foreach ($rows as $row) {
            $model = clone $this->model;

            foreach ($row as $key => $value) {
                $model->$key = $value;
            }

            $model->id = (int)($row['id'] ?? null);
            $models[] = $model;
        }

        return $models;
    }

    public function findById(int $id): ?Entity
    {
        $this->queryBuilder
            ->select(['*'])
            ->from($this->model->getTableName())
            ->where('id = ?', [$id]);

        $stmt = $this->executeQuery($this->queryBuilder);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row)
        {
            foreach ($row as $key => $value)
            {
                $this->model->$key = $value;
            }
            $this->model->id = (int)$row['id'];

            return $this->model;
        }

        return null;
    }

    /**
     * Удаляет запись по ID
     * @param int $id
     * @return bool true при успешном удалении, false при ошибке
     */
    public function deleteById(int $id): bool
    {
        $this->queryBuilder
            ->delete($this->model->getTableName())
            ->from($this->model->getTableName())
            ->where('id = ?', [$id]);

        try
        {
            $stmt = $this->executeQuery($this->queryBuilder);
            return $stmt->rowCount() > 0;
        }
        catch (\PDOException $e)
        {
            error_log('Delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Создаёт новую запись на основе текущего состояния модели
     * @return Entity|null Возвращает созданный объект с заполненными полями (id, created_at и т. п.) или null при ошибке
     */
    public function create(): ?Entity
    {
        $data = $this->model->getFields();
        unset($data['id']);

        $columns = array_keys($data);
        $values = array_values($data);

        $this->queryBuilder
            ->insert($this->model->getTableName(), $columns)
            ->values($values);

        $stmt = $this->executeQuery($this->queryBuilder);

        if ($stmt->rowCount() === 0)
        {
            return null;
        }

        $insertId = (int)$this->pdo->lastInsertId();
        $this->model->id = $insertId;

        return $this->findById($insertId);
    }


    /**
     * Обновляет запись по ID на основе текущего состояния модели
     * @param int $id
     * @return bool true при успешном обновлении, false при ошибке
     */
    public function updateById(int $id): bool
    {
        $data = $this->model->getFields();
        unset($data['id']);

        $this->queryBuilder
            ->update($this->model->getTableName())
            ->set($data)
            ->where('id = ?', [$id]);

        try {
            $stmt = $this->executeQuery($this->queryBuilder);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log('Update failed: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Проверяет существование записи по ID
     * @param int $id
     * @return bool true, если запись существует
     */
    public function exists(int $id): bool
    {
        $this->queryBuilder
            ->select(['COUNT(*) as count'])
            ->from($this->model->getTableName())
            ->where('id = ?', [$id]);

        $stmt = $this->executeQuery($this->queryBuilder);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int)$result['count'] > 0;
    }

    protected function executeQuery(IQueryBuilder $builder): \PDOStatement
    {
        $sql = $builder->getSql();
        $params = $builder->getParams();
        $builder->clearParams();

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
