<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\FieldTypeInterface;

use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\ValueObject\FieldConfig;

interface FieldType
{
    public function setConfig(FieldConfig $fieldConfig): FieldType;
    public function getConfig(): FieldConfig;
    public function addToForm(
        FormBuilderInterface $formBuilder,
        Section $section,
        $sectionEntity, // This can be any entity generated for a section
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface;
    public function directory(): string;
}
