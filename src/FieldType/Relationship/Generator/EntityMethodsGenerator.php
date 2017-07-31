<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;

class EntityMethodsGenerator implements Generator
{
    public static function generate(Field $field): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        $asString = (string) TemplateLoader::load( FullyQualifiedClassNameConverter::toDir(
                $field->getFieldType()->getFullyQualifiedClassName()
            ) . '/GeneratorTemplate/entity.methods.php',
            [
                'name' => 'testing',
                'handle' => 'suuuuuper'
            ]
        );

        print_r($asString);
        //print_r($fieldConfig); $fieldConfig≥¬
        exit;
    }
}
