<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\Entity\EntityInterface\Section;

interface Generator
{
    public function generateBySection(Section $section): void;
    public function getBuildMessages(): array;
}
