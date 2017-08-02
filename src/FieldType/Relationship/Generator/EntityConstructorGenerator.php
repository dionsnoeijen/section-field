<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Doctrine\Common\Util\Inflector;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityConstructorGenerator implements Generator
{
    public static function generate(Field $field, ...$options): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        return Template::create((string) TemplateLoader::load(
            FullyQualifiedClassNameConverter::toDir(
                $field->getFieldType()->getFullyQualifiedClassName()
            ) . '/GeneratorTemplate/entity.constructor.php', [
                'kind' => $fieldConfig['field']['kind'],
                'pluralPropertyName' => Inflector::pluralize($fieldConfig['field']['to'])
            ]
        ));
    }
}
