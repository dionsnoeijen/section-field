<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\SectionFieldInterface;

use Tardigrades\SectionField\ValueObject\Handle;

interface ReadSection
{
    public function read(Handle $sectionHandle, array $options): \ArrayIterator;
}
