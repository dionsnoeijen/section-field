<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldTypeInterface\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class Form
{
    /** @var SectionManager */
    private $sectionManager;

    /** @var FormFactory */
    private $formFactory;

    public function __construct(
        SectionManager $sectionManager,
        FormFactory $formFactory
    ) {
        $this->sectionManager = $sectionManager;
        $this->formFactory = $formFactory;;
    }

    public function buildFormForSection(Section $section): FormInterface
    {
        $sectionFullyQualifiedClassName = (string) $section->getConfig()->getFullyQualifiedClassName();
        $sectionEntity = new $sectionFullyQualifiedClassName;

        $form = $this->formFactory->createBuilder(FormType::class)->create($sectionEntity);

        /** @var Field $field */
        foreach ($section->getFields() as $field) {
            $fieldTypeFulluQualifiedClassName = (string) $field->getFieldType()->getFullyQualifiedClassName();
            /** @var FieldType $fieldType */
            $fieldType = new $fieldTypeFulluQualifiedClassName;
            $fieldType->setConfig($field->getConfig());
            $fieldType->addToForm($form);
        }

        return $form->getForm();
    }
}
