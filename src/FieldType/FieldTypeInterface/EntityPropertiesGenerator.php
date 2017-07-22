<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\FieldType\ValueObject\EntityPropertiesTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;

interface EntityPropertiesGenerator
{
    public function renderEntityProperties(FieldConfig $fieldConfig): EntityPropertiesTemplate;
}
