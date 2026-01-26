<?php

namespace Modules\Validator;

class StringValidator extends BaseValidator
{
    private ?int $minLength;
    private ?int $maxLength;
    private ?array $allowedChars;

    public function __construct(
        ?int   $minLength = null,
        ?int   $maxLength = null,
        ?array $allowedChars = null
    )
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->allowedChars = $allowedChars;
    }

    public function validate(mixed $value): bool
    {
        if (!is_string($value))
        {
            $this->setError('Значение должно быть строкой');
            return false;
        }

        if ($this->minLength !== null && strlen($value) < $this->minLength)
        {
            $this->setError("Длина строки должна быть не менее {$this->minLength} символов");
            return false;
        }

        if ($this->maxLength !== null && strlen($value) > $this->maxLength)
        {
            $this->setError("Длина строки не должна превышать {$this->maxLength} символов");
            return false;
        }

        if ($this->allowedChars !== null)
        {
            foreach (str_split($value) as $char)
            {
                if (!in_array($char, $this->allowedChars))
                {
                    $this->setError('Строка содержит недопустимые символы');
                    return false;
                }
            }
        }

        return true;
    }
}