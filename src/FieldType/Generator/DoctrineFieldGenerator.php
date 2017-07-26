<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class DoctrineFieldGenerator implements Generator
{

    public static function generate(Field $field, ...$managers): Template
    {
        $asString = (string) TemplateLoader::load(
            FullyQualifiedClassNameConverter::toDir(
            $field->getFieldType()->getFullyQualifiedClassName()
            ) . '/GeneratorTemplate/doctrine.config.xml.template'
        );

        $asString = str_replace(
            '{{ handle }}',
            $field->getConfig()->getHandle(),
            $asString
        );

        return Template::create($asString);
    }
}
