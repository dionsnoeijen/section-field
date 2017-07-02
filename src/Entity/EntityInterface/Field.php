<?php
declare (strict_types=1);

namespace Tardigrades\Entity\EntityInterface;

use Doctrine\Common\Collections\Collection;
use Tardigrades\Entity\Field as FieldEntity;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Name;
use Tardigrades\SectionField\ValueObject\Updated;


interface Field
{
    public function getId(): Id;
    public function getName(): Name;
    public function setName(Name $name): FieldEntity;
    public function getHandle(): Handle;
    public function setHandle(string $handle): FieldEntity;
    public function addSection(Section $section): FieldEntity;
    public function removeSection(Section $section): FieldEntity;
    public function getSections(): Collection;
    public function setFieldType(FieldType $fieldType): FieldEntity;
    public function getFieldType(): FieldType;
    public function setConfig(array $config): FieldEntity;
    public function getConfig(): FieldConfig;
    public function setCreated(\DateTime $created): FieldEntity;
    public function getCreated(): Created;
    public function setUpdated(\DateTime $updated): FieldEntity;
    public function getUpdated(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
