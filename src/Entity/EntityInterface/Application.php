<?php
declare (strict_types=1);

namespace Tardigrades\Entity\EntityInterface;

use Doctrine\Common\Collections\Collection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\Updated;

interface Application
{
    public function setId(int $id): Application;
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function setName(string $name): Application;
    public function getName(): Name;
    public function setHandle(string $handle): Application;
    public function getHandle(): Handle;
    public function getLanguages(): Collection;
    public function addLanguage(Language $language): Application;
    public function removeLanguage(Language $language): Application;
    public function getSections(): Collection;
    public function addSection(Section $section): Application;
    public function removeSection(Section $section): Application;
    public function setCreated(\DateTime $created): Application;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): Application;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
