<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\SectionField\Service\ReadOptions;
use Tardigrades\SectionField\ValueObject\SectionConfig;

interface ReadSection
{
    public function read(ReadOptions $options, SectionConfig $sectionConfig = null): \ArrayIterator;
}
