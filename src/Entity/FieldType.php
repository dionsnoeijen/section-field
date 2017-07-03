<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tardigrades\Entity\EntityInterface\FieldType as FieldTypeInterface;
use Tardigrades\Entity\EntityInterface\Field as FieldInterface;
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
    protected $namespace;

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

    public function setId(int $id): FieldType
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
        return Id::create($this->id);
    }

    public function getType(): Type
    {
        return Type::create($this->type);
    }

    public function setType(string $type): FieldType
    {
        $this->type = $type;

        return $this;
    }

    public function addField(FieldInterface $field): FieldType
    {
        if ($this->fields->contains($field)) {
            return $this;
        }
        $field->setFieldType($this);
        $this->fields->add($field);

        return $this;
    }

    public function removeField(FieldInterface $field): FieldType
    {
        if (!$this->fields->contains($field)) {
            return $this;
        }
        $this->fields->remove($field);

        return $this;
    }

    public function getFields(): ArrayCollection
    {
        return $this->fields;
    }

    public function setNamespace(string $namespace): FieldType
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getNamespace(): FullyQualifiedClassName
    {
        return FullyQualifiedClassName::create($this->namespace);
    }

    public function setCreated(\DateTime $created): FieldType
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
        return Created::create($this->created);
    }

    public function setUpdated(\DateTime $updated): FieldType
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
        return Updated::create($this->updated);
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

    public function getName(): Name
    {
        return Name::create($this->type);
    }

    public function setName(string $name): FieldType
    {
        $this->type = $name;

        return $this;
    }
}
