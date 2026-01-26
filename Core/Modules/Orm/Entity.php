<?php

namespace Modules\Orm;

class Entity implements \JsonSerializable
{
    protected string $table;
    public ?int $id = null;
    protected array $fields = [];

    public function getTableName(): string
    {
        return $this->table;
    }

    public function getFields(): array
    {
        return array_merge(['id' => $this->id], $this->fields);
    }

    public function jsonSerialize(): mixed
    {
        return $this->getFields();
    }

    public function __get(string $name)
    {
        return $this->fields[$name] ?? null;
    }

    public function __set(string $name, $value)
    {
        $this->fields[$name] = $value;
    }
}