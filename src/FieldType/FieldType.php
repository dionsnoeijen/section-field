<?php
declare (strict_types=1);

namespace Tardigrades\FieldType;

use Tardigrades\FieldType\FieldTypeInterface\FieldType as FieldTypeInterface;
use Tardigrades\FieldType\ValueObject\EntityMethodsTemplate;
use Tardigrades\FieldType\ValueObject\EntityPropertiesTemplate;
use Tardigrades\SectionField\Generator\Loader\TemplateLoader;
use Tardigrades\SectionField\ValueObject\FieldConfig;

abstract class FieldType implements FieldTypeInterface
{
    /** @var FieldConfig */
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
}
