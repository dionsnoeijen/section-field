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

class EntityPropertiesGenerator implements Generator
{
    public static function generate(Field $field, ...$options): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        /** @var SectionConfig $sectionConfig */
        $sectionConfig = $options[0]['sectionConfig'];

        return Template::create((string) TemplateLoader::load(
            FullyQualifiedClassNameConverter::toDir(
                $field->getFieldType()->getFullyQualifiedClassName()
            ) . '/GeneratorTemplate/entity.properties.php', [
                'kind' => $fieldConfig['field']['kind'],
                'pluralPropertyName' => Inflector::pluralize($fieldConfig['field']['to']),
                'entity' => ucfirst($fieldConfig['field']['to']),
                'propertyName' => $fieldConfig['field']['to']
            ]
        ));
    }
}
