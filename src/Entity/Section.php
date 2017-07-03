<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tardigrades\Entity\EntityInterface\Section as SectionInterface;
use Tardigrades\Entity\EntityInterface\Field as FieldInterface;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Updated;

class Section implements SectionInterface
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

    public function __construct(
        Collection $fields = null
    ) {
        $this->fields = is_null($fields) ? new ArrayCollection() : $fields;
    }

    public function setId(int $id): SectionInterface
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

    public function getName(): Name
    {
        return Name::create($this->name);
    }

    public function setName(string $name): SectionInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getHandle(): Handle
    {
        return Handle::create($this->handle);
    }

    public function setHandle(string $handle): SectionInterface
    {
        $this->handle = $handle;

        return $this;
    }

    public function addField(FieldInterface $field): SectionInterface
    {
        if ($this->fields->contains($field)) {
            return $this;
        }
        $this->fields->add($field);
        $field->addSection($this);

        return $this;
    }

    public function removeField(FieldInterface $field): SectionInterface
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

    public function setConfig(array $config): SectionInterface
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig(): SectionConfig
    {
        return SectionConfig::create($this->config);
    }

    public function setCreated(\DateTime $created): SectionInterface
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

    public function setUpdated(\DateTime $updated): SectionInterface
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
