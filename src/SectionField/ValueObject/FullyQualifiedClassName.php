<?php

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class FullyQualifiedClassName {

    /**
     * @var string
     */
    private $fullyQualifiedClassName;

    public function __construct(string $fullyQualifiedClassName)
    {
        Assertion::string($fullyQualifiedClassName, 'The fully qualified class name needs to be a string');

        $this->fullyQualifiedClassName = $fullyQualifiedClassName;
    }

    public function getClassName()
    {
        $type = explode('\\', $this->fullyQualifiedClassName);
        $className = $type[count($type) - 1];

        return $className;
    }

    public function __toString(): string
    {
        return $this->fullyQualifiedClassName;
    }

    public static function create(string $fullyQualifiedClassName): self
    {
        return new self($fullyQualifiedClassName);
    }
}
