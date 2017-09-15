<?php

namespace Tardigrades\FieldType\TextInput;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\SectionInterface;
use Tardigrades\FieldType\FieldType;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class TextInput extends FieldType
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        SectionInterface $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface {

        $options = $this->formOptions($sectionEntity);

        $formBuilder->add(
            (string) $this->getConfig()->getHandle(),
            TextType::class,
            $options
        );

        return $formBuilder;
    }
}
