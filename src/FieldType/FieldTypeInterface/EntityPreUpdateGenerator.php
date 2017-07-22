<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\FieldType\ValueObject\PreUpdateTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;

interface EntityPreUpdateGenerator
{
    public function renderPreUpdate(FieldConfig $fieldConfig): PreUpdateTemplate;
}
