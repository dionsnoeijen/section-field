<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class Label
{
    /** @var string */
    private $label;

    private function __construct(string $label)
    {
        Assertion::string($label, 'The label has to be a string');

        $this->label = $label;
    }

    public function __toString(): string
    {
        return $this->label;
    }

    public static function fromString(string $label): self
    {
        return new self($label);
    }
}
