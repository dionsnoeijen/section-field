<?php

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class SlugField
{
    /** @var string */
    private $slugField = '';

    private function __construct(string $value)
    {
        Assertion::nullOrNotEmpty($value, 'Value is not specified');

        $this->slugField = $value;
    }

    public function __toString(): string
    {
        return $this->slugField;
    }

    public static function fromString(string $slugField): self
    {
        return new static($slugField);
    }
}
