<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tardigrades\Entity\EntityInterface\Application as ApplicationInterface;
use Tardigrades\Entity\EntityInterface\Language;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Updated;

class Application implements ApplicationInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $handle;

    /** @var ArrayCollection */
    protected $languages;

    /** @var ArrayCollection */
    protected $sections;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    public function __construct(
        Collection $languages = null,
        Collection $sections = null
    ) {
        $this->languages = is_null($languages) ? new ArrayCollection() : $languages;
        $this->sections = is_null($sections) ? new ArrayCollection() : $sections;
    }

    public function setId(int $id): ApplicationInterface
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

    public function setName(string $name): ApplicationInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): Name
    {
        return Name::create($this->name);
    }

    public function setHandle(string $handle): ApplicationInterface
    {
        $this->handle = $handle;

        return $this;
    }

    public function getHandle(): Handle
    {
        return Handle::create($this->handle);
    }

    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): ApplicationInterface
    {
        if ($this->languages->contains($language)) {
            return $this;
        }
        $this->languages->add($language);

        return $this;
    }

    public function removeLanguage(Language $language): ApplicationInterface
    {
        if (!$this->languages->contains($language)) {
            return $this;
        }
        $this->languages->removeElement($language);

        return $this;
    }

    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): ApplicationInterface
    {
        if ($this->sections->contains($section)) {
            return $this;
        }
        $this->sections->add($section);

        return $this;
    }

    public function removeSection(Section $section): ApplicationInterface
    {
        if (!$this->sections->contains($section)) {
            return $this;
        }
        $this->sections->removeElement($section);

        return $this;
    }

    public function setCreated(\DateTime $created): ApplicationInterface
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

    public function setUpdated(\DateTime $updated): ApplicationInterface
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
