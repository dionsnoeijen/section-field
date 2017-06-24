<?php

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class FieldConfig
{
    /** @var int */
    protected $id;

    /** @var Field */
    protected $field;

    /** @var \stdClass */
    protected $config;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    public function getId(): int
    {
        return $this->id;
    }

    public function setField(Field $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getField(): ArrayCollection
    {
        return $this->field;
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
