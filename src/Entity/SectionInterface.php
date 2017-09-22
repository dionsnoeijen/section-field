<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\Collection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Updated;
use Tardigrades\SectionField\ValueObject\Version;

interface SectionInterface
{
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function getName(): Name;
    public function setName(string $name): SectionInterface;
    public function getHandle(): Handle;
    public function setHandle(string $handle): SectionInterface;
    public function addField(FieldInterface $field): SectionInterface;
    public function removeField(FieldInterface $field): SectionInterface;
    public function getFields(): Collection;
    public function addApplication(ApplicationInterface $application): SectionInterface;
    public function removeApplication(ApplicationInterface $application): SectionInterface;
    public function getApplications(): Collection;
    public function setConfig(array $config): SectionInterface;
    public function getConfig(): SectionConfig;
    public function setCreated(\DateTime $created): SectionInterface;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): SectionInterface;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function setVersion(int $version): SectionInterface;
    public function getVersion(): Version;
}
