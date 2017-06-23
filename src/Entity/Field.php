<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Field
{
    /** @var int */
    protected $id;

    /** @var string **/
    protected $name;

    /** @var string */
    protected $handle;

    /** @var ArrayCollection */
    protected $sections;

    /** @var FieldType */
    protected $fieldType;

    /** @var FieldConfig */
    protected $fieldConfig;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
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

    public function addSection(Section $section): void
    {
        if ($this->sections->contains($section)) {
            return;
        }
        $this->sections->add($section);
        $section->addField($this);
    }

    public function removeSection(Section $section): void
    {
        if ($this->sections->contains($section)) {
            return;
        }
        $this->sections->remove($section);
        $section->removeField($this);
    }

    public function setFieldType(FieldType $fieldType)
    {
        $fieldType->addField($this);
        $this->fieldType = $fieldType;
    }

    public function setFieldConfig(FieldConfig $fieldConfig)
    {
        $fieldConfig->addField($this);
        $this->fieldConfig = $fieldConfig;
    }

    public function getSections(): ArrayCollection
    {
        return $this->sections;
    }

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }
}
