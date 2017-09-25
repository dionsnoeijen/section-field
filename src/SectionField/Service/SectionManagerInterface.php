<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\ValueObject\Handle;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;

interface SectionManagerInterface
{
    public function create(SectionInterface $entity): SectionInterface;
    public function read(Id $id): SectionInterface;
    public function readAll(): array;
    public function update(SectionInterface $entity): void;
    public function delete(SectionInterface $entity): void;
    public function createByConfig(SectionConfig $sectionConfig): SectionInterface;
    public function updateByConfig(
        SectionConfig $sectionConfig,
        SectionInterface $section,
        bool $history = true
    ): SectionInterface;
    public function restoreFromHistory(SectionInterface $sectionFromHistory): SectionInterface;
    public function getRelationshipsOfAll(): array;
    public function readByHandle(Handle $handle): SectionInterface;
    public function readByHandles(array $handles): array;
}
