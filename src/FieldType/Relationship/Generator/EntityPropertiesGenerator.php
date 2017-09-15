<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Doctrine\Common\Util\Inflector;
use Tardigrades\Entity\FieldInterface;
use Tardigrades\FieldType\Generator\GeneratorInterface;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityPropertiesGenerator implements GeneratorInterface
{
    public static function generate(FieldInterface $field, ...$options): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        return Template::create((string) TemplateLoader::load(
            $field->getFieldType()->getInstance()->directory() .
            '/GeneratorTemplate/entity.properties.php', [
                'kind' => $fieldConfig['field']['kind'],
                'pluralPropertyName' => Inflector::pluralize($fieldConfig['field']['to']),
                'entity' => ucfirst($fieldConfig['field']['to']),
                'propertyName' => $fieldConfig['field']['to']
            ]
        ));
    }
}
