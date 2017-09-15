<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Updated;

interface LanguageInterface
{
    public function setId(int $id): LanguageInterface;
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function setI18n(string $i18n): LanguageInterface;
    public function getI18n(): I18n;
    public function addApplication(ApplicationInterface $application): LanguageInterface;
    public function removeApplication(ApplicationInterface $application): LanguageInterface;
    public function getApplications(): ArrayCollection;
    public function setCreated(\DateTime $created): LanguageInterface;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): LanguageInterface;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
