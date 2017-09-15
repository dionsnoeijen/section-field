<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Service;

use Tardigrades\SectionField\ValueObject\SectionConfig;

interface ReadSectionInterface
{
    public function read(ReadOptionsInterface $options, SectionConfig $sectionConfig = null): \ArrayIterator;
}
