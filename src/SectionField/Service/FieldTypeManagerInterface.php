<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\Entity\FieldTypeInterface;

interface FieldTypeManagerInterface
{
    public function create(FieldTypeInterface $entity): FieldTypeInterface;
    public function read(Id $id): FieldTypeInterface;
    public function readAll(): array;
    public function update(): void;
    public function delete(FieldTypeInterface $entity): void;
    public function createWithFullyQualifiedClassName(
        FullyQualifiedClassName $fullyQualifiedClassName
    ): FieldTypeInterface;
    public function readByType(Type $type): FieldTypeInterface;
    public function readByFullyQualifiedClassName(
        FullyQualifiedClassName $fullyQualifiedClassName
    ): FieldTypeInterface;
}
