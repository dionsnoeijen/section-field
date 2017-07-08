<?php
declare (strict_types=1);

namespace Tardigrades\Entity\EntityInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\I18n;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Updated;

interface Language
{
    public function setId(int $id): Language;
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function setI18n(string $i18n): Language;
    public function getI18n(): I18n;
    public function addApplication(Application $application): Language;
    public function removeApplication(Application $application): Language;
    public function getApplications(): ArrayCollection;
    public function setCreated(\DateTime $created): Language;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): Language;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
