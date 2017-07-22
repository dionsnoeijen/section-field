<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\SectionField\ValueObject\FieldConfig;

interface Generator
{
    public function setConfig(FieldConfig $fieldConfig): Generator;
    public function getConfig(): FieldConfig;
}
