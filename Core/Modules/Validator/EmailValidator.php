<?php

namespace Modules\Validator;

class EmailValidator extends BaseValidator
{
    public function validate(mixed $value): bool
    {
        if (!is_string($value))
        {
            $this->setError('Значение должно быть строкой');
            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            $this->setError('Некорректный email-адрес');
            return false;
        }

        return true;
    }
}