<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\FieldInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class DoctrineFieldGenerator implements GeneratorInterface
{
    public static function generate(FieldInterface $field): Template
    {
        $asString = (string) TemplateLoader::load(
            $field->getFieldType()->getInstance()->directory()
            . '/GeneratorTemplate/doctrine.config.xml.template'
        );

        $asString = str_replace(
            '{{ handle }}',
            $field->getConfig()->getHandle(),
            $asString
        );

        return Template::create($asString);
    }
}
