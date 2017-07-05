<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Language;
use Tardigrades\Entity\EntityInterface\FieldTranslation as FieldTranslationInterface;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Label;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Updated;

class FieldTranslation implements FieldTranslationInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $handle;

    /** @var string */
    protected $label;

    /** @var Field */
    protected $field;

    /** @var Language */
    protected $language;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    public function setId(int $id): FieldTranslationInterface
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return Name::create($this->name);
    }

    public function setName(string $name): FieldTranslationInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getHandle(): Handle
    {
        return Handle::create($this->handle);
    }

    public function setHandle(string $handle): FieldTranslationInterface
    {
        $this->handle = $handle;

        return $this;
    }

    public function getLabel(): Label
    {
        return Label::create($this->label);
    }

    public function setLabel(string $label): FieldTranslationInterface
    {
        $this->label = $label;

        return $this;
    }

    public function getField(): Field
    {
        return $this->field;
    }

    public function setField(Field $field): FieldTranslationInterface
    {
        $this->field = $field;

        return $this;
    }

    public function removeField(Field $field): FieldTranslationInterface
    {
        $this->field = null;

        return $this;
    }

    public function setLanguage(Language $language): FieldTranslationInterface
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setCreated(\DateTime $created): FieldTranslationInterface
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

    public function setUpdated(\DateTime $updated): FieldTranslationInterface
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
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
    }

    public function onPreUpdate(): void
    {
        $this->updated = new \DateTime('now');
    }
}
