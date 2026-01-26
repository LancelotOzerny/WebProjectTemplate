<?php

namespace Modules\Validator;

interface IValidator
{
    public function validate(mixed $value): bool;
    public function getError(): ?string;
}