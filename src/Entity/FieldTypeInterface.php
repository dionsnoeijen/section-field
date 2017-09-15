<?php
declare (strict_types=1);

namespace Tardigrades\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Tardigrades\SectionField\ValueObject\Created;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;
use Tardigrades\SectionField\ValueObject\Updated;
use Tardigrades\FieldType\FieldTypeInterface as FieldTypeInstance;

interface FieldTypeInterface
{
    public function setId(int $id): FieldTypeInterface;
    public function getId(): ?int;
    public function getIdValueObject(): Id;
    public function getType(): Type;
    public function setType(string $type): FieldTypeInterface;
    public function addField(FieldInterface $field): FieldTypeInterface;
    public function removeField(FieldInterface $field): FieldTypeInterface;
    public function getFields(): ArrayCollection;
    public function setFullyQualifiedClassName(string $fullyQualifiedClassName): FieldTypeInterface;
    public function getFullyQualifiedClassName(): FullyQualifiedClassName;
    public function setCreated(\DateTime $created): FieldTypeInterface;
    public function getCreated(): \DateTime;
    public function getCreatedValueObject(): Created;
    public function setUpdated(\DateTime $updated): FieldTypeInterface;
    public function getUpdated(): \DateTime;
    public function getUpdatedValueObject(): Updated;
    public function onPrePersist(): void;
    public function onPreUpdate(): void;
    public function getInstance(): FieldTypeInstance;
}
