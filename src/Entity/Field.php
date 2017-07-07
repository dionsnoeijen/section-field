<?php
declare (strict_types = 1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tardigrades\Entity\EntityInterface\Field as FieldInterface;
use Tardigrades\Entity\EntityInterface\FieldTranslation;
use Tardigrades\Entity\EntityInterface\FieldType as FieldTypeInterface;
use Tardigrades\Entity\EntityInterface\Section as SectionInterface;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Updated;

class Field implements FieldInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $handle;

    /** @var ArrayCollection */
    protected $sections;

    /** @var FieldType */
    protected $fieldType;

    /** @var Collection */
    protected $fieldTranslations;

    /** @var array */
    protected $config;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    public function __construct(
        Collection $sections = null,
        Collection $translations = null
    ) {
        $this->sections = is_null($sections) ? new ArrayCollection() : $sections;
        $this->fieldTranslations = is_null($translations) ? new ArrayCollection() : $translations;
    }

    public function setId(int $id): FieldInterface
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setHandle(string $handle): FieldInterface
    {
        $this->handle = $handle;

        return $this;
    }

    public function getHandle(): Handle
    {
        return Handle::create($this->handle);
    }

    public function getIdValueObject(): Id
    {
        return Id::create($this->id);
    }

    public function addSection(SectionInterface $section): FieldInterface
    {
        if ($this->sections->contains($section)) {
            return $this;
        }
        $this->sections->add($section);
        $section->addField($this);

        return $this;
    }

    public function removeSection(SectionInterface $section): FieldInterface
    {
        if ($this->sections->contains($section)) {
            return $this;
        }
        $this->sections->remove($section);
        $section->removeField($this);

        return $this;
    }

    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function getFieldTranslations(): Collection
    {
        return $this->fieldTranslations;
    }

    public function addFieldTranslation(FieldTranslation $fieldTranslation): FieldInterface
    {
        if ($this->fieldTranslations->contains($fieldTranslation)) {
            return $this;
        }
        $this->fieldTranslations->add($fieldTranslation);
        $fieldTranslation->setField($this);

        return $this;
    }

    public function removeFieldTranslation(FieldTranslation $fieldTranslation): FieldInterface
    {
        if (!$this->fieldTranslations->contains($fieldTranslation)) {
            return $this;
        }
        $this->fieldTranslations->removeElement($fieldTranslation);
        $fieldTranslation->removeField($this);

        return $this;
    }

    public function setFieldType(FieldTypeInterface $fieldType): FieldInterface
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    public function removeFieldType(FieldTypeInterface $fieldType): FieldInterface
    {
        $this->fieldType = null;

        return $this;
    }

    public function getFieldType(): FieldTypeInterface
    {
        return $this->fieldType;
    }

    public function setConfig(array $config): FieldInterface
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig(): FieldConfig
    {
        return FieldConfig::create((array) $this->config);
    }

    public function setCreated(\DateTime $created): FieldInterface
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

    public function setUpdated(\DateTime $updated): FieldInterface
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
}
