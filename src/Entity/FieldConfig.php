<?php

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class FieldConfig
{
    /** @var int */
    protected $id;

    /** @var ArrayCollection */
    protected $fields;

    /** @var \stdClass */
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

    public function addField(Field $field): void
    {
        $this->fields->add($field);
    }

    public function getFields(): ArrayCollection
    {
        return $this->fields;
    }

    public function setConfig(\stdClass $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): \stdClass
    {
        return $this->config;
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
