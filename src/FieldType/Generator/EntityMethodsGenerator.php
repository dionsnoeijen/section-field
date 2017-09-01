<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityMethodsGenerator implements Generator
{

    public static function generate(Field $field): Template
    {
        $asString = (string) TemplateLoader::load(
            $field->getFieldType()
                ->getInstance()
                ->directory() .
            '/GeneratorTemplate/entitymethods.php.template'
        );

        $asString = str_replace(
            '{{ methodName }}',
            $field->getConfig()->getMethodName(),
            $asString
        );
        $asString = str_replace(
            '{{ propertyName }}',
            $field->getConfig()->getPropertyName(),
            $asString
        );

        return Template::create($asString);
    }
}
