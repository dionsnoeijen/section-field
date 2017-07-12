<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator\Loader;

class TemplateLoader
{
    public static function load(string $location): string
    {
        if (\file_exists($location)) {
            return \file_get_contents($location);
        }
        throw new TemplateNotFoundException();
    }
}
