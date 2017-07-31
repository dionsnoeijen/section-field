<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\FieldType\ValueObject\Template;

interface Generator
{
    public static function generate(Field $field): Template;
}
