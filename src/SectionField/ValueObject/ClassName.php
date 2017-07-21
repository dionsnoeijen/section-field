<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;
use Tardigrades\Helper\StringConverter;

final class ClassName
{
    /** @var string */
    private $className;

    private function __construct(string $className)
    {
        Assertion::string($className, 'ClassName must be a string');

        $this->className = $className;
    }

    public function __toString(): string
    {
        return $this->className;
    }

    public static function fromString(string $className): self
    {
        StringConverter::toCamelCase($className);

        return new self(ucfirst($className));
    }
}
