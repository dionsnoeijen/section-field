<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Tardigrades\Entity\EntityInterface\Application;
use Tardigrades\Entity\EntityInterface\Language as LanguageInterface;
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

    /** @var Application */
    protected $application;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

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
        return Id::create($this->id);
    }

    public function setI18n(string $i18n): LanguageInterface
    {
        $this->i18n = $i18n;

        return $this;
    }

    public function getI18n(): I18n
    {
        return I18n::create($this->i18n);
    }

    public function setApplication(Application $application): LanguageInterface
    {
        $this->application = $application;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function removeApplication(Application $application): LanguageInterface
    {
        if ($this->application === $application) {
            $this->application = null;
            $application->removeLanguage($this);
        }
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
        return Created::create($this->created);
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
        return Updated::create($this->updated);
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
