<?php
declare (strict_types=1);

namespace Tardigrades\Entity\EntityInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;
use Tardigrades\SectionField\ValueObject\Updated;
use Tardigrades\Entity\FieldType as FieldTypeEntity;

interface FieldType
{
    public function setId(int $id): FieldTypeEntity;
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function getType(): Type;
    public function setType(string $type): FieldTypeEntity;
    public function addField(Field $field): FieldTypeEntity;
    public function removeField(Field $field): FieldTypeEntity;
    public function getFields(): ArrayCollection;
    public function setNamespace(string $namespace): FieldTypeEntity;
    public function getNamespace(): FullyQualifiedClassName;
    public function setCreated(\DateTime $created): FieldTypeEntity;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): FieldTypeEntity;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
}
