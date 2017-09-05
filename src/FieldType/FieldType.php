<?php
declare (strict_types=1);

namespace Tardigrades\FieldType;

use Mockery\Exception;
use ReflectionClass;
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

    public function formOptions($sectionEntity): array
    {
        $fieldConfig = $this->getConfig()->toArray();
        $options = [];
        if (!empty($fieldConfig['field']['form'])) {
            $entryId = $sectionEntity->getId();
            $options = $fieldConfig['field']['form']['all'];
            if (empty($entryId) && !empty($fieldConfig['field']['form']['create'])) {
                $options = array_merge($options, $fieldConfig['field']['form']['create']);
            }
            if (!empty($entryId) && !empty($fieldConfig['field']['form']['update'])) {
                $options = array_merge($options, $fieldConfig['field']['form']['update']);
            }
        }
        return $options;
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

    public function directory(): string
    {
        $fieldType = new ReflectionClass($this);
        return pathinfo($fieldType->getFilename(), PATHINFO_DIRNAME);
    }

    abstract public function addToForm(
        FormBuilderInterface $formBuilder,
        Section $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface;
}
