<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;

class EntityPropertiesGenerator implements Generator
{
    public static function generate(Field $field, ...$managers): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        print_r($fieldConfig);
        exit;
    }
}
