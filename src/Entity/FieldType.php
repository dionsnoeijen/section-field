<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tardigrades\FieldType\FieldTypeInterface as FieldTypeInstance;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\Type;
use Tardigrades\SectionField\ValueObject\Updated;

class FieldType implements FieldTypeInterface
{
    /** @var int */
    protected $id;

    /** @var string * */
    protected $type;

    /** @var string */
    protected $fullyQualifiedClassName;

    /** @var ArrayCollection */
    protected $fields;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    public function __construct(
        Collection $fields = null
    ) {
        $this->fields = is_null($fields) ? new ArrayCollection() : $fields;
    }

    public function setId(int $id): FieldTypeInterface
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdValueObject(): Id
    {
        return Id::fromInt($this->id);
    }

    public function getType(): Type
    {
        return Type::fromString($this->type);
    }

    public function setType(string $type): FieldTypeInterface
    {
        $this->type = $type;

        return $this;
    }

    public function addField(FieldInterface $field): FieldTypeInterface
    {
        if ($this->fields->contains($field)) {
            return $this;
        }
        $field->setFieldType($this);
        $this->fields->add($field);

        return $this;
    }

    public function removeField(FieldInterface $field): FieldTypeInterface
    {
        if (!$this->fields->contains($field)) {
            return $this;
        }
        $this->fields->remove($field);

        return $this;
    }

    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function hasFields(): bool
    {
        return !$this->fields->isEmpty();
    }

    public function setFullyQualifiedClassName(string $fullyQualifiedClassName): FieldTypeInterface
    {
        $this->fullyQualifiedClassName = $fullyQualifiedClassName;

        return $this;
    }

    public function getFullyQualifiedClassName(): FullyQualifiedClassName
    {
        return FullyQualifiedClassName::fromString($this->fullyQualifiedClassName);
    }

    public function getName(): Name
    {
        return Name::fromString($this->type);
    }

    public function setName(string $name): FieldTypeInterface
    {
        $this->type = $name;

        return $this;
    }

    public function setCreated(\DateTime $created): FieldTypeInterface
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function getCreatedValueObject(): Created
    {
        return Created::fromDateTime($this->created);
    }

    public function setUpdated(\DateTime $updated): FieldTypeInterface
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    public function getUpdatedValueObject(): Updated
    {
        return Updated::fromDateTime($this->updated);
    }

    public function onPrePersist(): void
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
    }

    public function onPreUpdate(): void
    {
        $this->updated = new \DateTime("now");
    }

    public function getInstance(): FieldTypeInstance
    {
        $fieldType = (string) $this->getFullyQualifiedClassName();
        return new $fieldType();
    }
}
