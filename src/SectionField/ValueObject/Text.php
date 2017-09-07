<?php

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class Text
{
    /** @var string */
    private $value = '';

    private function __construct(string $value)
    {
        Assertion::nullOrNotEmpty($value, 'Value is not specified');
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new static($value);
    }
}
