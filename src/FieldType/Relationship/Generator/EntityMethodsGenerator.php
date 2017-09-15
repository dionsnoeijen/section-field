<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Doctrine\Common\Util\Inflector;
use Tardigrades\Entity\FieldInterface;
use Tardigrades\FieldType\Generator\GeneratorInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class EntityMethodsGenerator implements GeneratorInterface
{
    public static function generate(FieldInterface $field, ...$options): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        /** @var SectionConfig $sectionConfig */
        $sectionConfig = $options[0]['sectionConfig'];

        return Template::create((string) TemplateLoader::load(
            $field->getFieldType()->getInstance()->directory() .
            '/GeneratorTemplate/entity.methods.php', [
                'kind' => $fieldConfig['field']['kind'],
                'pluralMethodName' => ucfirst(Inflector::pluralize($fieldConfig['field']['to'])),
                'pluralPropertyName' => Inflector::pluralize($fieldConfig['field']['to']),
                'methodName' => ucfirst($fieldConfig['field']['to']),
                'entity' => ucfirst($fieldConfig['field']['to']),
                'propertyName' => $fieldConfig['field']['to'],
                'thatMethodName' => $sectionConfig->getClassName()
            ]
        ));
    }
}
