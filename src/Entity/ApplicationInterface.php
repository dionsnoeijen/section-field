<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\Collection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\Updated;

interface ApplicationInterface
{
    public function setId(int $id): ApplicationInterface;
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function setName(string $name): ApplicationInterface;
    public function getName(): Name;
    public function setHandle(string $handle): ApplicationInterface;
    public function getHandle(): Handle;
    public function getLanguages(): Collection;
    public function addLanguage(LanguageInterface $language): ApplicationInterface;
    public function removeLanguage(LanguageInterface $language): ApplicationInterface;
    public function getSections(): Collection;
    public function addSection(SectionInterface $section): ApplicationInterface;
    public function removeSection(SectionInterface $section): ApplicationInterface;
    public function setCreated(\DateTime $created): ApplicationInterface;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): ApplicationInterface;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
