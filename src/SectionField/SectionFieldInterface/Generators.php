<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\Entity\EntityInterface\Section;

interface Generators
{
    public function generateBySection(Section $section): array;
    public function getBuildMessages(): array;
    public function getWritables(): array;
}
