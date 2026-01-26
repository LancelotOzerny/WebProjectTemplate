<?php

namespace Modules\Validator;

abstract class BaseValidator implements IValidator
{
    protected ?string $error = null;

    public function getError(): ?string
    {
        return $this->error;
    }

    protected function setError(string $message): void
    {
        $this->error = $message;
    }
}