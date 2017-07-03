<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\SectionField\ValueObject\Id;
use Tardigrades\SectionField\ValueObject\SectionConfig;

interface SectionManager
{
    public function create(Section $entity): Section;
    public function read(Id $id): Section;
    public function readAll(): array;
    public function update(Section $entity): Section;
    public function delete(Section $entity): void;
    public function createByConfig(SectionConfig $sectionConfig): Section;
    public function updateByConfig(SectionConfig $sectionConfig, Section $section): Section;
}
