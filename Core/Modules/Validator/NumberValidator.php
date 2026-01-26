<?php

namespace Modules\Validator;

class NumberValidator extends BaseValidator
{
    private ?float $min;
    private ?float $max;
    private bool $integerOnly;

    public function __construct(
        ?float $min = null,
        ?float $max = null,
        bool   $integerOnly = false
    )
    {
        $this->min = $min;
        $this->max = $max;
        $this->integerOnly = $integerOnly;
    }

    public function validate(mixed $value): bool
    {
        if (!is_numeric($value))
        {
            $this->setError('Значение должно быть числом');
            return false;
        }

        $numValue = (float)$value;

        if ($this->integerOnly && !ctype_digit((string)$value))
        {
            $this->setError('Значение должно быть целым числом');
            return false;
        }

        if ($this->min !== null && $numValue < $this->min)
        {
            $this->setError("Значение должно быть не меньше {$this->min}");
            return false;
        }

        if ($this->max !== null && $numValue > $this->max)
        {
            $this->setError("Значение не должно превышать {$this->max}");
            return false;
        }

        return true;
    }
}