<?php
declare (strict_types=1);
namespace Tardigrades\FieldType\FieldTypeInterface;

use Tardigrades\FieldType\ValueObject\DoctrineXmlFieldsTemplate;
use Tardigrades\SectionField\ValueObject\FieldConfig;

interface DoctrineOneToManyGenerator
{
    public function renderField(FieldConfig $fieldConfig): DoctrineXmlFieldsTemplate;
}
