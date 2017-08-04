<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

class ReadOptions
{
    /** @var array */
    private $options;

    private function __construct(
        array $options
    ) {
        $this->options = $options;
    }

    

    public static function fromArray(array $options): self
    {
        return new self($options);
    }
}
