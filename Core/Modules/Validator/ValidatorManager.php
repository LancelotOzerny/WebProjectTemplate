<?php

namespace Modules\Validator;

class ValidatorManager
{
    private array $validators = [];

    public function addValidator(string $name, IValidator $validator): void
    {
        $this->validators[$name] = $validator;
    }

    public function getValidator(string $name): ?IValidator
    {
        return $this->validators[$name] ?? null;
    }

    public function validate(string $name, mixed $value): array
    {
        $validator = $this->getValidator($name);
        if (!$validator)
        {
            return ['valid' => false, 'error' => "Валидатор {$name} не найден"];
        }

        $isValid = $validator->validate($value);
        return [
            'valid' => $isValid,
            'error' => $isValid ? null : $validator->getError()
        ];
    }
}