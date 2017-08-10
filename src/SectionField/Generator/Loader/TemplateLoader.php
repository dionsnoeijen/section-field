<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator\Loader;

class TemplateLoader
{
    public static function load(string $location, array $variables = []): string
    {
        if (\file_exists($location)) {
            if (pathinfo($location, PATHINFO_EXTENSION) === 'php') {
                ob_start();
                extract($variables);
                include $location;
                return ob_get_clean();
            } else {
                return \file_get_contents($location);
            }
        }
        throw new TemplateNotFoundException($location . ': template not found');
    }
}
