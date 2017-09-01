<?php
declare (strict_types=1);

namespace Tardigrades\Helper;

use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

class FullyQualifiedClassNameConverter
{
    public static function toHandle(FullyQualifiedClassName $fullyQualifiedClassName): string
    {
        $handle = explode('\\', (string) $fullyQualifiedClassName);
        $handle = end($handle);
        return lcfirst($handle);
    }
}
