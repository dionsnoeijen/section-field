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

    public function __construct(string $type)
    {
        Assertion::string($type, 'The type has to be a string');

        $this->type = $type;
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public static function create(string $type): self
    {
        return new self($type);
    }
}
