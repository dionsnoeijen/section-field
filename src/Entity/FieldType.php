<?php

namespace Entity;

use Doctrine\Common\Collections\ArrayCollection;

class FieldType
{
    /** @var int */
    protected $id;

    /** @var string * */
    protected $type;

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

    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }
}
