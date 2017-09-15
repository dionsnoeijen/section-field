<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\SectionInterface;

interface GeneratorsInterface
{
    public function generateBySection(SectionInterface $section): array;
    public function getBuildMessages(): array;
    public function getWritables(): array;
}
