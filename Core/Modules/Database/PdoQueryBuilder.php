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

    /**
     * Формирует запрос INSERT INTO
     * @param string $table Имя таблицы
     * @param array $columns Массив имён колонок (например, ['name', 'email'])
     * @return self
     */
    public function insert(string $table, array $columns): self
    {
        $colList = implode(', ', $columns);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $this->sql = "INSERT INTO $table ($colList) VALUES ($placeholders)";
        return $this;
    }

    public function delete($table) : self
    {
        $this->sql = " DELETE";
        return $this;
    }

    public function set(array $data): self
    {
        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "$column = ?";
            $this->params[] = $value;
        }

        $setClause = implode(', ', $setParts);
        $this->sql .= " SET $setClause";

        return $this;
    }

    /**
     * Формирует запрос UPDATE
     * @param string $table Имя таблицы
     * @return self
     */
    public function update(string $table): self
    {
        $this->sql = "UPDATE $table";
        return $this;
    }

    /**
     * Добавляет значения для INSERT (должен вызываться после insert())
     * @param array $values Массив значений в порядке, соответствующем колонкам
     * @return self
     */
    public function values(array $values): self
    {
        $this->params = array_merge($this->params, $values);
        return $this;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function clearParams() : self
    {
        $this->params = [];
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
