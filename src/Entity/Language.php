<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Updated;

class Language implements LanguageInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $i18n;

    /** @var ArrayCollection */
    protected $applications;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    public function __construct(
        Collection $applications = null
    ) {
        $this->applications = is_null($applications) ? new ArrayCollection() : $applications;
    }

    public function setId(int $id): LanguageInterface
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

    public function setI18n(string $i18n): LanguageInterface
    {
        $this->i18n = $i18n;

        return $this;
    }

    public function getI18n(): I18n
    {
        return I18n::fromString($this->i18n);
    }

    public function addApplication(ApplicationInterface $application): LanguageInterface
    {
        if ($this->applications->contains($application)) {
            return $this;
        }
        $this->applications->add($application);

        return $this;
    }

    public function getApplications(): ArrayCollection
    {
        return $this->applications;
    }

    public function removeApplication(ApplicationInterface $application): LanguageInterface
    {
        if (!$this->applications->contains($application)) {
            return $this;
        }
        $this->applications->removeElement($application);

        return $this;
    }

    public function setCreated(\DateTime $created): LanguageInterface
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

    public function setUpdated(\DateTime $updated): LanguageInterface
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
        $this->updated = new \DateTime('now');
        $this->created = new \DateTime('now');
    }

    public function onPreUpdate(): void
    {
        $this->updated = new \DateTime('now');
    }
}
