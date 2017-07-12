<?php

namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\FieldType\ValueObject\EntityMethodsTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;

interface FieldType
{
    public function setConfig(FieldConfig $fieldConfig): FieldType;
    public function getConfig(): FieldConfig;
    public function getEntityMethodsTemplate(): EntityMethodsTemplate;
    public function renderEntityMethods(): EntityMethodsTemplate;
}
