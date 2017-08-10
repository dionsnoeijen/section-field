<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Form;

use ReflectionClass;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldTypeInterface\FieldType;
use Tardigrades\FieldType\Slug\ValueObject\Slug;
use Tardigrades\Helper\FullyQualifiedClassNameConverter;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\SectionFieldInterface\Form as SectionFormInterface;
use Tardigrades\SectionField\ValueObject\FullyQualifiedClassName;
use Tardigrades\SectionField\ValueObject\ReadOptions;

class Form implements SectionFormInterface
{
    /** @var SectionManager */
    private $sectionManager;

    /** @var FormFactory */
    private $formFactory;

    /** @var ReadSection */
    private $readSection;

    public function __construct(
        SectionManager $sectionManager,
        FormFactory $formFactory,
        ReadSection $readSection
    ) {
        $this->sectionManager = $sectionManager;
        $this->formFactory = $formFactory;
        $this->readSection = $readSection;
    }

    public function buildFormForSection(
        FullyQualifiedClassName $forHandle,
        Slug $slug = null
    ): FormInterface {

        $section = $this->getSection($forHandle);
        $sectionEntity = $this->getSectionEntity($forHandle, $section, $slug);
        $form = $this->formFactory
            ->createBuilder(
                FormType::class,
                $sectionEntity,
                ['method' => 'POST']
            );

        /** @var Field $field */
        foreach ($section->getFields() as $field) {
            $fieldTypeFulluQualifiedClassName = (string) $field
                ->getFieldType()
                ->getFullyQualifiedClassName();
            /** @var FieldType $fieldType */
            $fieldType = new $fieldTypeFulluQualifiedClassName;
            $fieldType->setConfig($field->getConfig());

            $reflector = new ReflectionClass($fieldType);
            $method = $reflector->getMethod('addToForm');
            $options = null;
            if (isset($method->getParameters()[1])) {
                $options = [
                    'sectionManager' => $this->sectionManager,
                    'readSection' => $this->readSection,
                    'sectionEntity' => $sectionEntity
                ];
            }
            $fieldType->addToForm($form, $options);
        }

        $form->add('save', SubmitType::class);
        return $form->getForm();
    }

    private function getSection(
        FullyQualifiedClassName $forHandle
    ): Section {
        return $this->sectionManager->readByHandle(
            FullyQualifiedClassNameConverter::toHandle($forHandle)
        );
    }

    private function getSectionEntity(
        FullyQualifiedClassName $forHandle,
        Section $section,
        Slug $slug = null
    ) {
        if (!empty($slug)) {
            $sectionEntity = $this->readSection->read(ReadOptions::fromArray([
                'section' => $forHandle,
                'slug' => $slug
            ]))->current();
        }

        if (empty($sectionEntity)) {
            $sectionFullyQualifiedClassName = (string) $section->getConfig()->getFullyQualifiedClassName();
            $sectionEntity = new $sectionFullyQualifiedClassName;
        }

        return $sectionEntity;
    }
}
