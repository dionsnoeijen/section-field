<?php

namespace Tardigrades\SectionField;

use Tardigrades\FieldType\FieldTypeInterface\FieldType as FieldTypeInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

abstract class FieldType implements FieldTypeInterface
{
    /** @var FieldConfig $fieldConfig */
    private $fieldConfig;

    public function setConfig(FieldConfig $fieldConfig)
    {
        $this->fieldConfig = $fieldConfig;
    }

    public function getConfig(): FieldConfig
    {
        return $this->fieldConfig;
    }
}
