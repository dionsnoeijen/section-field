<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\Entity\FieldInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;

interface FieldManagerInterface
{
    public function create(FieldInterface $entity): FieldInterface;
    public function read(Id $id): FieldInterface;
    public function readAll(): array;
    public function update(): void;
    public function delete(FieldInterface $entity): void;
    public function createByConfig(FieldConfig $fieldConfig): FieldInterface;
    public function updateByConfig(FieldConfig $fieldConfig, FieldInterface $field): FieldInterface;
    public function readByHandle(Handle $handle): FieldInterface;
    public function readByHandles(array $fields): array;
}
