<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\FieldType\ValueObject\EntityMethodsTemplate;
use Tardigrades\FieldType\ValueObject\EntityPropertiesTemplate;
use Tardigrades\FieldType\ValueObject\PrePersistTemplate;
use Tardigrades\FieldType\ValueObject\PreUpdateTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;

interface EntityGenerator
{
    public function renderEntityMethods(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): EntityMethodsTemplate;
    public function renderEntityProperties(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): EntityPropertiesTemplate;
    public function renderPrePersist(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): PrePersistTemplate;
    public function renderPreUpdate(FieldConfig $config, FullyQualifiedClassName $fullyQualifiedClassName): PreUpdateTemplate;
}
