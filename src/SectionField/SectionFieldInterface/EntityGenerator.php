<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\Entity\EntityInterface\Section;

interface EntityGenerator
{
    public function generateBySection(Section $section): void;
}
