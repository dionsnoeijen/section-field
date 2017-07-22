<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\FieldType\ValueObject\EntityMethodsTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;

interface EntityMethodsGenerator
{
    public function renderEntityMethods(FieldConfig $fieldConfig): EntityMethodsTemplate;
}
