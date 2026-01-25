<?php

namespace Modules\Database;

interface IQueryBuilder
{
    public function select(array $columns): self;
    public function from(string $table): self;
    public function where(string $condition, array $params = []): self;
    public function andWhere(string $condition, array $params = []): self;
    public function orWhere(string $condition, array $params = []): self;
    public function limit(int $limit): self;
    public function offset(int $offset): self;
    public function getSql(): string;
    public function getParams(): array;
}
