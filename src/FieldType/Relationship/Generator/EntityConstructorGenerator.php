<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;

class EntityConstructorGenerator implements Generator
{
    public static function generate(Field $field): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        return Template::create('');
    }
}
