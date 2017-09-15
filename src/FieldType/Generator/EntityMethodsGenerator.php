<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Generator;

use Tardigrades\Entity\FieldInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityMethodsGenerator implements GeneratorInterface
{
    public static function generate(FieldInterface $field): Template
    {
        $asString = (string) TemplateLoader::load(
            $field->getFieldType()
                ->getInstance()
                ->directory() .
            '/GeneratorTemplate/entity.methods.php.template'
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
