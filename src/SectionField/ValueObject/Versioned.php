<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

final class Versioned
{
    /** @var \DateTime */
    private $versioned;

    private function __construct(\DateTime $created)
    {
        $this->versioned = $created;
    }

    public function __toString(): string
    {
        return $this->versioned->format(\DateTime::ATOM);
    }

    public function getDateTime(): \DateTime
    {
        return $this->versioned;
    }

    public static function fromDateTime(\DateTime $created): self
    {
        return new self($created);
    }
}
