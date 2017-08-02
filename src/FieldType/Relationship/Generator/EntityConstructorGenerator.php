<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Doctrine\Common\Util\Inflector;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\ValueObject\SectionConfig;

class EntityConstructorGenerator implements Generator
{
    public static function generate(Field $field, ...$options): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        /** @var SectionConfig $sectionConfig */
        $sectionConfig = $options[0]['sectionConfig'];

        return Template::create((string) TemplateLoader::load(
            FullyQualifiedClassNameConverter::toDir(
                $field->getFieldType()->getFullyQualifiedClassName()
            ) . '/GeneratorTemplate/entity.constructor.php', [
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
