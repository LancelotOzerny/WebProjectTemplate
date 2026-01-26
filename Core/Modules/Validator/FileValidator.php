<?php

namespace Modules\Validator;

class FileValidator extends BaseValidator
{
    private array $allowedMimeTypes;
    private ?int $maxSize;

    public function __construct(array $allowedMimeTypes, ?int $maxSize = null)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->maxSize = $maxSize;
    }

    public function validate(mixed $value): bool
    {
        if (!$value instanceof \SplFileInfo)
        {
            $this->setError('Значение должно быть объектом SplFileInfo');
            return false;
        }

        if ($this->maxSize !== null && $value->getSize() > $this->maxSize)
        {
            $this->setError("Размер файла превышает допустимый лимит в {$this->maxSize} байт");
            return false;
        }

        $mimeType = mime_content_type($value->getPathname());
        if (!in_array($mimeType, $this->allowedMimeTypes))
        {
            $this->setError("Недопустимый тип файла. Разрешены: " . implode(', ', $this->allowedMimeTypes));
            return false;
        }

        return true;
    }
}