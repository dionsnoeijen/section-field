<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class SectionFullyQualifiedClassName
{
    /**
     * @var string
     */
    private $fullyQualifiedClassName;

    private function __construct(string $fullyQualifiedClassName)
    {
        Assertion::string($fullyQualifiedClassName, 'The name has to be a string');

        $this->fullyQualifiedClassName = $fullyQualifiedClassName;
    }

    public function __toString(): string
    {
        return $this->fullyQualifiedClassName;
    }

    public static function fromNamespaceAndClassName(SectionNamespace $namespace, ClassName $className)
    {
        return new self((string) $namespace . '\\' . (string) $className);
    }

    public static function fromString(string $fullyQualifiedClassName): self
    {
        return new self($fullyQualifiedClassName);
    }
}
