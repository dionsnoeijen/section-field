<?php
declare (strict_types=1);

namespace Tardigrades\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\SectionField\Service\ReadSectionInterface;
use Tardigrades\SectionField\Service\SectionManagerInterface;
use Tardigrades\SectionField\ValueObject\FieldConfig;

interface FieldTypeInterface
{
    public function setConfig(FieldConfig $fieldConfig): FieldTypeInterface;
    public function getConfig(): FieldConfig;
    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        $sectionEntity, // This can be any entity generated for a section
        SectionManagerInterface $sectionManager,
        ReadSectionInterface $readSection
    ): FormBuilderInterface;
    public function directory(): string;
}
