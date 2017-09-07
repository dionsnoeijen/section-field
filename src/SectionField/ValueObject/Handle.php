<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class Handle
{
    /** @var string */
    private $handle;

    private function __construct(string $handle)
    {
        Assertion::string($handle, 'The handle must be a strijg');

        $this->handle = $handle;
    }

    public function __toString()
    {
        return $this->handle;
    }

    public static function fromString(string $handle): self
    {
        return new self($handle);
    }
}
