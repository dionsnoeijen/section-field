<?php
declare (strict_types=1);
namespace Tardigrades\SectionField\SectionFieldInterface;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\Type;
use Tardigrades\Entity\FieldType;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
interface FieldTypeManager
{
    public function create(FieldType $entity): FieldType;
    public function read(Id $id): FieldType;
    public function readAll(): array;
    public function update(FieldType $entity): FieldType;
    public function delete(FieldType $entity): void;
    public function createWithFullyQualifiedClassName(FullyQualifiedClassName $fullyQualifiedClassName): FieldType;
    public function readByType(Type $type): FieldType;
}
