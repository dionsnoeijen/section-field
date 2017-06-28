<?php

namespace Tardigrades\SectionField\SectionFieldInterface;

interface Manager
{
    public function create(StructureEntity $entity): StructureEntity;
    public function read(int $id): StructureEntity;
    public function update(StructureEntity $entity): StructureEntity;
    public function delete(StructureEntity $entity): bool;
}
