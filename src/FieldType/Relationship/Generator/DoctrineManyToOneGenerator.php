<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\Relationship\Generator;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\FieldTypeInterface\Generator;
use Tardigrades\FieldType\ValueObject\Template;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class DoctrineManyToOneGenerator implements Generator
{
    const KIND = 'many-to-one';

    public static function generate(Field $field): Template
    {
        $fieldConfig = $field->getConfig()->toArray();

        if ($fieldConfig['field']['kind'] === self::KIND) {

            /** @var SectionManager $sectionManager */

            print_r($fieldConfig['field']['to']);
            echo PHP_EOL;
            echo PHP_EOL;

            return Template::create('');
        }
    }
}
