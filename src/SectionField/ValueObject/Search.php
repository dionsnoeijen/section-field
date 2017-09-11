<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class Search
{
    /** @var string */
    private $search;

    private function __construct(string $search)
    {
        Assertion::string($search, 'Search is supposed to be passed as a string');

        $this->search = $search;
    }

    public function __toString(): string
    {
        return $this->search;
    }

    public static function fromString(string $search): self
    {
        return new self($search);
    }
}
