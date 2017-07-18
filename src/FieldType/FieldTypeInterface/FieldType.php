<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\SectionField\ValueObject\FieldConfig;

interface FieldType
{
    public function setConfig(FieldConfig $fieldConfig): FieldType;
    public function getConfig(): FieldConfig;
}
