<?php

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tardigrades\SectionField\SectionFieldInterface\StructureEntity;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class Section implements StructureEntity
{
    /** @var int */
    protected $id;

    /** @var string **/
    protected $name;

    /** @var string */
    protected $handle;

    /** @var ArrayCollection */
    protected $fields;

    /** @var array */
    protected $config;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function setHandle(string $handle): void
    {
        $this->handle = $handle;
    }

    public function addField(Field $field)
    {
        if ($this->fields->contains($field)) {
            return;
        }
        $this->fields->add($field);
        $field->addSection($this);
    }

    public function removeField(Field $field)
    {
        if ($this->fields->contains($field)) {
            return;
        }
        $this->fields->remove($field);
    }

    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function setConfig(\stdClass $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): SectionConfig
    {
        return SectionConfig::create($this->config);
    }

    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function setUpdated(\DateTime $updated): void
    {
        $this->updated = $updated;
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
}
