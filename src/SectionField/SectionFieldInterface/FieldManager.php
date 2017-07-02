<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\Entity\Field;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Id;

interface FieldManager
{
    public function create(Field $entity): Field;
    public function read(Id $id): Field;
    public function readAll(): array;
    public function update(Field $entity): Field;
    public function delete(Field $entity): void;
    public function createByConfig(FieldConfig $fieldConfig): Field;
    public function updateByConfig(FieldConfig $fieldConfig, Field $field): Field;
    public function readFieldsByHandles(array $fields): array;
}

