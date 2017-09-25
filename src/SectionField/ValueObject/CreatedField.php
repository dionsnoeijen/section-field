<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class CreatedField
{
    /** @var string */
    private $createdField = '';

    private function __construct(string $value)
    {
        Assertion::nullOrNotEmpty($value, 'Value is not specified');

        $this->createdField = $value;
    }

    public function __toString(): string
    {
        return $this->createdField;
    }

    public static function fromString(string $createdField): self
    {
        return new static($createdField);
    }
}
