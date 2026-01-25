<?php

namespace Modules\Database;

class PdoQueryBuilder implements IQueryBuilder
{
    private string $sql = '';
    private array $params = [];
    private bool $hasWhere = false;

    public function select(array $columns): self
    {
        $cols = empty($columns) ? '*' : implode(', ', $columns);
        $this->sql = "SELECT $cols";
        return $this;
    }

    public function from(string $table): self
    {
        $this->sql .= " FROM $table";
        return $this;
    }

    public function where(string $condition, array $params = []): self
    {
        $this->sql .= " WHERE $condition";
        $this->params = array_merge($this->params, $params);
        $this->hasWhere = true;
        return $this;
    }

    public function andWhere(string $condition, array $params = []): self
    {
        if (!$this->hasWhere) {
            return $this->where($condition, $params);
        }
        $this->sql .= " AND $condition";
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function orWhere(string $condition, array $params = []): self
    {
        if (!$this->hasWhere) {
            return $this->where($condition, $params);
        }
        $this->sql .= " OR $condition";
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->sql .= " LIMIT $limit";
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->sql .= " OFFSET $offset";
        return $this;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
