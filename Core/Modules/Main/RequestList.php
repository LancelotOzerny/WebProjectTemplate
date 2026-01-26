<?php

namespace Modules\Main;

class RequestList
{
    private array $data = [];

    public function __construct($params)
    {
        foreach ($params as $key => $value)
        {
            $this->data[$key] = $value;
        }
    }

    public function has($key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function get($key)
    {
        return $this->data[$key];
    }
}