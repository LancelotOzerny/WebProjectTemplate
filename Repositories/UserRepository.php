<?php

namespace Repositories;

use Modules\Orm\Entity;
use Modules\Orm\Repository;

class UserRepository extends Repository
{
    function findByEmail(string $email) : Entity | null
    {
        $this->queryBuilder
            ->select(['*'])
            ->from($this->model->getTableName())
            ->where('email = ?', [$email]);

        $stmt = $this->executeQuery($this->queryBuilder);
        $row = $stmt->fetch();

        if (!$row)
        {
            return null;
        }

        foreach ($row as $key => $value)
        {
            $this->model->$key = $value;
        }
        $this->model->id = $row['id'];

        return $this->model;
    }
}