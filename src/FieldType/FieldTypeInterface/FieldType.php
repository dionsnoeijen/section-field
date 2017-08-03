<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

interface FieldType
{
    public function setConfig(FieldConfig $fieldConfig): FieldType;
    public function getConfig(): FieldConfig;
    public function addToForm(FormBuilderInterface $formBuilder): FormBuilderInterface;
}
