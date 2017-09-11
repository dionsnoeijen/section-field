<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class Offset
{
    /** @var int */
    private $offset;

    private function __construct(int $offset)
    {
        Assertion::integer($offset, 'Offset is supposed to be an integer');

        $this->offset = $offset;
    }

    public function toInt(): int
    {
        return $this->offset;
    }

    public static function fromInt(int $offset): self
    {
        return new self($offset);
    }
}
