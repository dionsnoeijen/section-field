<?php
declare (strict_types=1);

namespace Tardigrades\Helper;

use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

class FullyQualifiedClassNameConverter
{
    public static function toDir(FullyQualifiedClassName $fullyQualifiedClassName): string
    {
        $segments = explode('\\',
            explode(
                '/src',
                __DIR__
            )[0] . '/src/' .
            str_replace(
                'Tardigrades\\', // @todo: The vendor name must be configurable, not hardcoded.
                '',
                (string) $fullyQualifiedClassName
            )
        );
        array_pop($segments);
        $dir = str_replace(
            '\\',
            '/',
            implode('\\', $segments)
        );

        return $dir;
    }

    public static function toHandle(FullyQualifiedClassName $fullyQualifiedClassName): string
    {
        $handle = explode('\\', (string) $fullyQualifiedClassName);
        $handle = end($handle);
        return lcfirst($handle);
    }
}
