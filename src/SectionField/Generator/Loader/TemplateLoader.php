<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator\Loader;

use Tardigrades\SectionField\ValueObject\FieldConfig;

class TemplateLoader
{
    public static function load(string $location, FieldConfig $config = null): string
    {
        if (\file_exists($location)) {
            if (pathinfo($location, PATHINFO_EXTENSION) === 'php') {
                ob_start();
                include $location;
                return ob_get_clean();
            } else {
                return \file_get_contents($location);
            }
        }
        throw new TemplateNotFoundException($location . ': template not found');
    }
}
