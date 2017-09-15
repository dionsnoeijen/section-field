<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Generator\Writer\Writable;

interface GeneratorInterface
{
    public function generateBySection(SectionInterface $section): Writable;
    public function getBuildMessages(): array;
}
