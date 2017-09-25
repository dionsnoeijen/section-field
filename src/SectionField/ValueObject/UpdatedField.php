<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class UpdatedField
{
    /** @var string */
    private $updatedField = '';

    private function __construct(string $value)
    {
        Assertion::nullOrNotEmpty($value, 'Value is not specified');

        $this->updatedField = $value;
    }

    public function __toString(): string
    {
        return $this->updatedField;
    }

    public static function fromString(string $updatedField): self
    {
        return new static($updatedField);
    }
}
