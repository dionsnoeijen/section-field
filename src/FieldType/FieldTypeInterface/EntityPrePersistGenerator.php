<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\FieldType\ValueObject\PrePersistTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;

interface EntityPrePersistGenerator
{
    public function renderPrePersist(FieldConfig $fieldConfig): PrePersistTemplate;
}
