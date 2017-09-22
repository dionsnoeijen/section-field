<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Updated;
use Tardigrades\SectionField\ValueObject\Version;

class Section implements SectionInterface, SectionEntityInterface
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

    /** @var ArrayCollection */
    protected $applications;

    /** @var ArrayCollection */
    protected $history;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    /** @var int */
    protected $version;

    public function __construct(
        Collection $fields = null,
        Collection $applications = null,
        Collection $history = null
    ) {
        $this->fields = is_null($fields) ? new ArrayCollection() : $fields;
        $this->applications = is_null($applications) ? new ArrayCollection() : $applications;
        $this->history = is_null($history) ? new ArrayCollection() : $history;
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
        return Id::fromInt($this->id);
    }

    public function getName(): Name
    {
        return Name::fromString($this->name);
    }

    public function setName(string $name): SectionInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getHandle(): Handle
    {
        return Handle::fromString($this->handle);
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

    public function removeFields(): SectionInterface
    {
        $this->fields->clear();

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
        return SectionConfig::fromArray($this->config);
    }

    public function addApplication(ApplicationInterface $application): SectionInterface
    {
        if ($this->applications->contains($application)) {
            return $this;
        }
        $this->applications->add($application);
        $application->addSection($this);

        return $this;
    }

    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function removeApplication(ApplicationInterface $application): SectionInterface
    {
        if (!$this->applications->contains($application)) {
            return $this;
        }
        $this->applications->remove($application);

        return $this;
    }

    public function setVersion(int $version): SectionInterface
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion(): Version
    {
        return Version::fromInt($this->version);
    }

    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(SectionInterface $section): SectionEntityInterface
    {
        if ($this->history->contains($section)) {
            return $this;
        }
        $this->history->add($section);
        $section->setSection($this);

        return $this;
    }

    public function removeHistory(SectionInterface $section): SectionEntityInterface
    {
        if (!$this->history->contains($section)) {
            return $this;
        }
        $this->history->remove($section);

        return $this;
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
        return Created::fromDateTime($this->created);
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
        return Updated::fromDateTime($this->updated);
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
