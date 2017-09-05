<?php
declare (strict_types=1);

namespace Tardigrades\FieldType\TextArea;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Tardigrades\Entity\EntityInterface\Section;
use Tardigrades\FieldType\FieldType;
use Tardigrades\FieldType\TextArea\TextAreaInterface\TextArea as TextAreaInterface;
use Tardigrades\SectionField\SectionFieldInterface\ReadSection;
use Tardigrades\SectionField\SectionFieldInterface\SectionManager;

class TextArea extends FieldType implements TextAreaInterface
{
    public function addToForm(
        FormBuilderInterface $formBuilder,
        Section $section,
        $sectionEntity,
        SectionManager $sectionManager,
        ReadSection $readSection
    ): FormBuilderInterface
    {

        $formBuilder->add(
            (string) $this->getConfig()->getHandle(),
            TextareaType::class
        );

        return $formBuilder;
    }
}
