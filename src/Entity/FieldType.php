<?php

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Tardigrades\SectionField\SectionFieldInterface\StructureEntity;

class FieldType implements StructureEntity
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

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function addField(Field $field): void
    {
        $this->fields->add($field);
    }

    public function getFields(): ArrayCollection
    {
        return $this->fields;
    }

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setUpdated(\DateTime $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    public function onPrePersist()
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
    }

    public function onPreUpdate()
    {
        $this->updated = new \DateTime("now");
    }

    public function getName(): string
    {
        return $this->type;
    }

    public function setName(string $name): string
    {
        $this->type = $name;
    }
}
