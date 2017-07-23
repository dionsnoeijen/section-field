<?php
declare (strict_types=1);

namespace Tardigrades\Helper;

class NamespaceConverter
{
    public static function toDir(string $namespace): string
    {
        $segments = explode('\\',
            explode(
                '/src',
                __DIR__
            )[0] . '/src/' .
            str_replace(
                'Tardigrades\\', // @todo: The vendor name must be configurable, not hardcoded.
                '',
                $namespace
            )
        );

        $dir = str_replace(
            '\\',
            '/',
            implode('\\', $segments)
        );

        $dir = str_replace(
            '//',
            '/',
            $dir
        );

        return $dir;
    }
}
