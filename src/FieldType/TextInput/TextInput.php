<?php

namespace Tardigrades\FieldType\TextInput;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class TextInput extends FieldType implements TextInputFieldType
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        Section $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface
    {
        $requiredFields = $section->getConfig()->toArray()['section']['required'];

        $formBuilder->add(
            (string) $this->getConfig()->getHandle(),
            TextType::class, [
                'required' => in_array((string) $this->getConfig()->getHandle(), $requiredFields)
            ]
        );

        return $formBuilder;
    }
}
