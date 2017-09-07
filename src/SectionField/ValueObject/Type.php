<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class Type
{
    /**
     * @var string
     */
    private $type;

    private function __construct(string $type)
    {
        Assertion::string($type, 'The type has to be a string');

        $this->type = $type;
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public static function fromString(string $type): self
    {
        return new self($type);
    }
}
