<?php

namespace Tardigrades\FieldType;

use Tardigrades\FieldType\FieldTypeInterface\FieldType as FieldTypeInterface;
use Tardigrades\FieldType\ValueObject\EntityMethodsTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;

abstract class FieldType implements FieldTypeInterface
{
    /** @var FieldConfig $fieldConfig */
    private $fieldConfig;

    public function setConfig(FieldConfig $fieldConfig): FieldTypeInterface
    {
        $this->fieldConfig = $fieldConfig;

        return $this;
    }

    public function getConfig(): FieldConfig
    {
        return $this->fieldConfig;
    }

    abstract public function getEntityMethodsTemplate(): EntityMethodsTemplate;
}
