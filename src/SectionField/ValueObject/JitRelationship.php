<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

class JitRelationship
{
    /** @var FullyQualifiedClassName */
    private $fullyQualifiedClassName;

    /** @var Id */
    private $id;

    private function __construct(
        FullyQualifiedClassName $fullyQualifiedClassName,
        Id $id
    ) {
        $this->fullyQualifiedClassName = $fullyQualifiedClassName;
        $this->id = $id;
    }

    public function getFullyQualifiedClassName()
    {
        return $this->fullyQualifiedClassName;
    }

    public function getId()
    {
        return $this->id;
    }

    public static function fromFullyQualifiedClassNameAndId(
        FullyQualifiedClassName $fullyQualifiedClassName,
        Id $id
    ): self {
        return new self($fullyQualifiedClassName, $id);
    }
}
