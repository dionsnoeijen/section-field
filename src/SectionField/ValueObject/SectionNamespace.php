<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class SectionNamespace
{
    /**
     * @var string
     */
    private $sectionNamespace;

    private function __construct(string $sectionNamespace)
    {
        Assertion::string($sectionNamespace, 'The name has to be a string');

        $this->sectionNamespace = $sectionNamespace;
    }

    public function __toString(): string
    {
        return $this->sectionNamespace;
    }

    public static function fromString(string $sectionNamespace): self
    {
        return new self($sectionNamespace);
    }
}
