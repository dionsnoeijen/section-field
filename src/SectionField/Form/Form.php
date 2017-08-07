<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Tardigrades\Entity\EntityInterface\Field;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldTypeInterface\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;
use Tardigrades\SectionField\SectionFieldInterface\Form as SectionFormInterface;

class Form implements SectionFormInterface
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
        $this->formFactory = $formFactory;
    }

    public function buildFormForSection(Section $section, $sectionEntity = null): FormInterface
    {
        if (empty($sectionEntity)) {
            $sectionFullyQualifiedClassName = (string)$section->getConfig()->getFullyQualifiedClassName();
            $sectionEntity = new $sectionFullyQualifiedClassName;
        }

        $form = $this->formFactory
            ->createBuilder(
                FormType::class,
                $sectionEntity,
                ['method' => 'POST']
            );

        /** @var Field $field */
        foreach ($section->getFields() as $field) {
            $fieldTypeFulluQualifiedClassName = (string) $field->getFieldType()->getFullyQualifiedClassName();
            /** @var FieldType $fieldType */
            $fieldType = new $fieldTypeFulluQualifiedClassName;
            $fieldType->setConfig($field->getConfig());
            $fieldType->addToForm($form);
        }

        $form->add('save', SubmitType::class);

        return $form->getForm();
    }
}
