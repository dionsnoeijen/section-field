<?php
declare (strict_types=1);

namespace Tardigrades\FieldType;

use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldTypeInterface\FieldType as FieldTypeInterface;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
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

    public function isRequired(Section $section): bool
    {
        try {
            $requiredFields = $section->getConfig()->getRequired();
        } catch (\Exception $exception) {
            $requiredFields = [];
        }

        return in_array(
            (string) $this->getConfig()->getHandle(),
            $requiredFields
        );
    }

    public function hasEntityEvent(string $event): bool
    {
        try {
            $entityEvents = $this->getConfig()->getEntityEvents();
        } catch (\Exception $exception) {
            $entityEvents = [];
        }

        return in_array($event, $entityEvents);
    }

    abstract public function addToForm(
        FormBuilderInterface $formBuilder,
        Section $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface;
}
