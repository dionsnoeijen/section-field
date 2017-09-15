<?php
declare (strict_types=1);

namespace Tardigrades\Helper;

use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\Handle;

class FullyQualifiedClassNameConverter
{
    public static function toHandle(FullyQualifiedClassName $fullyQualifiedClassName): Handle
    {
        $handle = explode('\\', (string) $fullyQualifiedClassName);
        $handle = end($handle);
        return Handle::fromString(lcfirst($handle));
    }
}
