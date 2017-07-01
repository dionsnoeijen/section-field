<?php
declare (strict_types=1);

namespace Tardigrades\Entity\EntityInterface;

use Doctrine\Common\Collections\Collection;
use Tardigrades\Entity\Field;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\SectionConfig;
use Tardigrades\SectionField\ValueObject\Updated;

interface Section
{
    public function getId(): Id;
    public function getName(): Name;
    public function setName(string $name): Section;
    public function getHandle(): Handle;
    public function setHandle(string $handle): Section;
    public function addField(Field $field): Section;
    public function removeField(Field $field): Section;
    public function getFields(): Collection;
    public function setConfig(\stdClass $config): Section;
    public function getConfig(): SectionConfig;
    public function setCreated(\DateTime $created): Section;
    public function getCreated(): Created;
    public function setUpdated(\DateTime $updated): Section;
    public function getUpdated(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
